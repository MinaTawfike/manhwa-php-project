<?php

namespace App\Http\Controllers;

use App\Models\Comic;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;


class ComicController extends Controller
{
    public function index(): View
    {
        $comics = Comic::with('chapters')->paginate(20);
        return view('comics.index', compact('comics'));
    }

    public function show(Comic $comic): View
    {
        $comic->load('chapters.pages');
        return view('comics.show', compact('comic'));
    }

    public function create(): View
    {
        return view('comics.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,avif|max:2048',
            'status' => 'required|in:ongoing,completed,hiatus',
        ]);
        
        $validated['user_id'] = auth()->id();
        $comic = Comic::create($validated);

        if ($request->hasFile('poster')) {
            
            $path = $this->uploadImage($request->file('poster'), $comic->id);
            $comic->update(['poster' => $path]);
        }

        
        return redirect()->route('comics.index')->with('success', 'Comic created successfully');
    }

    public function edit(Comic $comic): View
    {
        // Allow super admins or comic owners
        if (!auth()->user()->isSuperAdmin() && $comic->user_id !== auth()->id()) {
            abort(403);
        }
        
        return view('comics.edit', compact('comic'));
    }

    public function update(Request $request, Comic $comic): RedirectResponse
    {
        // Allow super admins or comic owners
        if (!auth()->user()->isSuperAdmin() && $comic->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,avif|max:2048',
            'status' => 'required|in:ongoing,completed,hiatus',
        ]);

        if ($request->hasFile('poster')) {
            $validated['poster'] = $this->uploadImage($request->file('poster'), $comic->id);
        }

        $comic->update($validated);
        return redirect()->route('comics.show', $comic)->with('success', 'Comic updated successfully');
    }

    public function destroy(Comic $comic): RedirectResponse
    {
        // Allow super admins or comic owners
        if (!auth()->user()->isSuperAdmin() && $comic->user_id !== auth()->id()) {
            abort(403);
        }

        $comic->delete();
        return redirect()->route('comics.index')->with('success', 'Comic deleted successfully');
    }

    public function bookmark(Comic $comic): RedirectResponse
    {
        auth()->user()->bookmarkedComics()->toggle($comic->id);
        $status = auth()->user()->bookmarkedComics()->where('comic_id', $comic->id)->exists() 
            ? 'bookmarked' 
            : 'unbookmarked';
        
        return back()->with('success', "Comic {$status}");
    }

    public function bookmarks(): View
    {
        $bookmarkedComics = auth()->user()
            ->bookmarkedComics()
            ->with(['userLastChapters' => function ($query) {
                $query->where('user_id', auth()->id());
            }])
            ->paginate(20);

        return view('bookmarks.index', compact('bookmarkedComics'));
    }

    private function uploadImage($image, $comicId)
    {
        // Placeholder for Cloudinary upload
        // TODO: Implement Cloudinary integration
        // For now, store locally
        $path = $image->storeAs("comics/{$comicId}", $image->hashName(), 'r2');
        return Storage::disk('r2')->url($path);
    }
}
