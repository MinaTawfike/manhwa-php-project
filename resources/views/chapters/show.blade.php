@extends('layouts.app')

@section('title', $comic->title . ' - Chapter ' . $chapter->number)

@section('content')
    <div class="reader-container">
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 2rem;">
            <div>
                <h2>{{ $comic->title }}</h2>
                <p style="color: #b0b0b0;">Chapter {{ $chapter->number }}
                    @if($chapter->name) - {{ $chapter->name }} @endif
                </p>
            </div>
            @auth
                <div style="display: flex; gap: 0.5rem;">
                    <a href="{{ route('chapters.edit', [$comic, $chapter]) }}" class="btn btn-secondary" style="padding: 0.5rem 1rem;">Edit</a>
                    <form action="{{ route('chapters.destroy', [$comic, $chapter]) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-secondary" style="padding: 0.5rem 1rem;" onclick="return confirm('Delete this chapter?')">Delete</button>
                    </form>
                </div>
            @endauth
        </div>

        @auth
            <div class="actions">
                <form action="{{ route('chapters.bookmark', $chapter) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-secondary">
                        @if(auth()->user()->bookmarkedChapters()->where('chapter_id', $chapter->id)->exists())
                            ❌ Unbookmark
                        @else
                            🔖 Bookmark
                        @endif
                    </button>
                </form>

                <div>
                    <p style="margin-bottom: 0.5rem; font-weight: bold;">Rate this chapter:</p>
                    <div class="rating-buttons">
                        @for($i = 1; $i <= 10; $i++)
                            <form action="{{ route('chapters.rate', $chapter) }}" method="POST" style="display: inline;">
                                @csrf
                                <input type="hidden" name="rating" value="{{ $i }}">
                                <button type="submit" class="rating-btn @if(auth()->user()->ratedChapters()->where('chapter_id', $chapter->id)->wherePivot('rating', $i)->exists()) active @endif">
                                    {{ $i }}
                                </button>
                            </form>
                        @endfor
                    </div>
                </div>
            </div>
        @endauth

        <div class="pages-container">
            <!-- CSS to visually show only top 50% of images (easy to tweak later) -->
            <style>
                .page-top-50 {
                    /* Constrain height to 50% of intrinsic height via container; we emulate by fixed aspect cropping */
                    /* Approach: Use a wrapper with overflow hidden; image object-position top */
                    width: 100%;
                    max-width: 900px; /* optional max width */
                    /*overflow: hidden;*/
                    margin: 0 auto 0px auto;
                }
                .page-top-50 img {
                    width: 100%;
                    /* Show the top part of the image */
                    object-fit: cover;     /* cover fills width, crop excess */
                    object-position: top;  /* crop from bottom, keep top */
                    /* The container height will effectively define "how much" to show */
                    display: block;
                }
                /* You can adjust the ratio below to change how much is visible (50% by default) */
                .page-top-50 {
                    aspect-ratio: 0.769 / 1; /* This is a proxy for 50% height; tweak as needed */
                }
                /* Alternative: fixed height relative to viewport
                .page-top-50 { height: 50vh; } img { height: 100%; } */
            </style>

            @foreach ($chapter->images as $img)
                <div class="page-top-50">
                    <img src="{{ asset('storage/'.$img->path) }}" alt="{{ $img->alt ?? 'Page '.$img->page_number }}">
                </div>
            @endforeach
         </div>
        @auth
            <div style="background-color: #3a3a3a; padding: 2rem; border-radius: 8px; margin-top: 2rem;">
                <h3 style="color: #ff6b6b; margin-bottom: 1rem;">Comment on this Chapter</h3>
                <form action="{{ route('chapters.comment', $chapter) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <textarea name="comment" placeholder="Your comment...">{{ $chapter->comment }}</textarea>
                    </div>
                    <button type="submit" class="btn">Save Comment</button>
                </form>
            </div>
        @endauth

        @if($chapter->comment)
            <div style="background-color: #3a3a3a; padding: 1.5rem; border-radius: 8px; margin-top: 2rem; border-left: 4px solid #ff6b6b;">
                <h4 style="color: #ff6b6b; margin-bottom: 0.5rem;">Chapter Comment</h4>
                <p>{{ $chapter->comment }}</p>
            </div>
        @endif
    </div>

    <div style="display: flex; justify-content: space-between; gap: 1rem; margin-top: 2rem;">
        <a href="{{ route('comics.show', $comic) }}" class="btn btn-secondary">← Back to Comic</a>
    </div>
@endsection
