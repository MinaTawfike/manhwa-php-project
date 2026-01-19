<?php

namespace App\Http\Controllers;

use App\Models\Comic;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

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
            'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:ongoing,completed,hiatus',
        ]);

        if ($request->hasFile('poster')) {
            $validated['poster'] = $this->uploadImage($request->file('poster'));
        }

        Comic::create($validated);
        return redirect()->route('comics.index')->with('success', 'Comic created successfully');
    }

    public function edit(Comic $comic): View
    {
        return view('comics.edit', compact('comic'));
    }

    public function update(Request $request, Comic $comic): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:ongoing,completed,hiatus',
        ]);

        if ($request->hasFile('poster')) {
            $validated['poster'] = $this->uploadImage($request->file('poster'));
        }

        $comic->update($validated);
        return redirect()->route('comics.show', $comic)->with('success', 'Comic updated successfully');
    }

    public function destroy(Comic $comic): RedirectResponse
    {
        $comic->delete();
        return redirect()->route('comics.index')->with('success', 'Comic deleted successfully');
    }

    private function uploadImage($image)
    {
        // Placeholder for Cloudinary upload
        // TODO: Implement Cloudinary integration
        // For now, store locally
        $path = $image->store('comics', 'public');
        return '/storage/' . $path;
    }
}
