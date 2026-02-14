<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Comic;
use App\Models\ChapterImage;
use App\Models\ComicUserLastChapter;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ChapterController extends Controller
{
    public function show(Comic $comic, Chapter $chapter): View
    {
        $chapter->load('pages', 'bookmarkedBy', 'ratedBy', 'images');
        
        // Track last chapter viewed (if authenticated)
        if (auth()->check()) {
            ComicUserLastChapter::updateOrCreate(
                [
                    'comic_id' => $comic->id,
                    'user_id' => auth()->id(),
                ],
                [
                    'chapter_id' => $chapter->id,
                ]
            );
        }
        
        return view('chapters.show', compact('comic', 'chapter'));
    }

    public function create(Comic $comic): View
    {
        return view('chapters.create', compact('comic'));
    }

    public function store(Request $request, Comic $comic): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'number' => 'required|integer',
            'comment' => 'nullable|string',
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,webp,avif', 'max:5120'],
        ]);

        $validated['comic_id'] = $comic->id;
        
        

        return DB::transaction(function () use ($request, $validated) {
            // Create chapter first
            $chapter = Chapter::create([
                'name' => $validated['name'],
                'number' => $validated['number'],
                'comment' => $validated['comment'],
                'comic_id' => $validated['comic_id'],
                // ... add other fields
            ]);

            // Handle images if provided
            $files = $request->file('images', []);
            $page = 1;

            foreach ($files as $file) {
                $path = $this->uploadImage($file, $chapter);

                ChapterImage::create([
                    'chapter_id' => $chapter->id,
                    'path' => $path,
                    'page_number' => $page++,
                    'alt' => null,
                ]);
            }

            return redirect()
                ->route('chapters.show', [$chapter->comic, $chapter])
                ->with('status', 'Chapter created successfully.');
        });
    }

    public function edit(Comic $comic, Chapter $chapter): View
    {
        $chapter->load(['images']);
        return view('chapters.edit', compact('comic', 'chapter'));
    }

    public function update(Request $request, Comic $comic, Chapter $chapter): RedirectResponse
    {
        //for reordering
        $request->merge([
            'order' => $request->order
                ? json_decode($request->order, true)
                : []
        ]);
        
        
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'number' => 'required|integer',
            'comment' => 'nullable|string',
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,webp,avif', 'max:5120'],

            // Reordering: array of image_id => page_number
            'order'   => 'nullable|array',
            'order.*' => 'integer|exists:chapter_images,id',

            // Deletions: array of image IDs to delete
            'delete_images' => ['nullable', 'array'],
            'delete_images.*' => ['integer', 'exists:chapter_images,id'],
        ]);

        DB::transaction(function () use ($request, $chapter, $validated) {
            // Update basic fields
            $chapter->update([
                'name' => $validated['name'],
                'number' => $validated['number'],
            ]);

            // 1) Handle deletions
            $deleteIds = $validated['delete_images'] ?? [];
            if ($deleteIds) {
                $toDelete = ChapterImage::where('chapter_id', $chapter->id)
                    ->whereIn('id', $deleteIds)
                    ->get();

                foreach ($toDelete as $img) {
                    // Remove underlying file (ignore if missing)
                    if ($img->path) {
                        Storage::disk('r2')->delete($img->path);
                    }
                    $img->delete();
                }
            }

            // 2) Handle new uploads (append to end)
            $files = $request->file('images', []);
            if ($files) {
                $nextPage = (int) $chapter->images()->count() + 1;
                foreach ($files as $file) {
                    $path = $this->uploadImage($file, $chapter);
                    ChapterImage::create([
                        'chapter_id' => $chapter->id,
                        'path' => $path,
                        'page_number' => $nextPage++,
                        'alt' => null,
                    ]);
                }
            }

            // 3) Handle reordering: set explicit page_number values

            
            if (!empty($validated['order'])) {
                //dd($validated['order']);

                DB::transaction(function () use ($validated, $chapter) {

                    // Step 1: assign temporary numbers
                    foreach ($validated['order'] as $index => $imageId) {
                        ChapterImage::where('chapter_id', $chapter->id)
                            ->where('id', $imageId)
                            ->update([
                                'page_number' => 1000 + $index + 1
                            ]);
                    }

                    // Step 2: normalize to 1..n
                    $chapter->images()->orderBy('page_number')->get()->each(function ($img, $i) {
                        $img->update([
                            'page_number' => $i + 1
                        ]);
                    });

                });

            }      
                




            // 4) Normalize page_number to 1..N without gaps and duplicates
            $ordered = $chapter->images()->get()->sortBy('page_number')->values();
            foreach ($ordered as $index => $img) {
                $desired = $index + 1;
                if ($img->page_number !== $desired) {
                    $img->update(['page_number' => $desired]);
                }
            }
        });

        return redirect()
            ->route('chapters.edit', [$chapter->comic, $chapter])
            ->with('status', 'Chapter updated successfully.');

    }

    public function destroy(Comic $comic, Chapter $chapter): RedirectResponse
    {
        $chapter->delete();
        return redirect()->route('comics.show', $comic)->with('success', 'Chapter deleted successfully');
    }


    private function uploadImage($image, $chapter)
    {
        // Placeholder for Cloudinary upload
        // TODO: Implement Cloudinary integration
        // For now, store locally
        $path = $image->storeAs("comics/{$chapter->comic->id}/chapter-id-{$chapter->id}", $image->hashName(), 'r2');
        return Storage::disk('r2')->url($path);
    }
}
