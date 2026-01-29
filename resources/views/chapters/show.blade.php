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

        <div class="pages-container" style="--image-crop-ratio: 0.5;">
            <style>
                .pages-container {
                    /* Change --image-crop-ratio to adjust visible portion (0.5 = 50%) */
                    --image-crop-ratio: 0.5;
                    display: flex;
                    flex-direction: column;
                    gap: 0; /* remove spacing between pages */
                    align-items: center;
                }

                .page-image-crop {
                    width: 100%;
                    max-width: 700px; /* optional max width for larger screens */
                    margin: 0; /* remove margin to eliminate gaps */
                    overflow: hidden;
                    background-color: #2a2a2a;
                }

                .page-image-crop img {
                    width: 100%;
                    /* Let JS compute the container height based on image's natural size
                       so the visible percentage is consistent across different widths. */
                    object-fit: cover;
                    object-position: top;
                    display: block;
                }
            </style>

            @foreach ($chapter->images as $img)
                <div class="page-image-crop">
                    <img loading="lazy" src="{{ asset('storage/'.$img->path) }}" alt="{{ $img->alt ?? 'Page '.$img->page_number }}">
                </div>
            @endforeach
        </div>

        <script>
            (function(){
                const container = document.querySelector('.pages-container');
                const cssRatio = getComputedStyle(container).getPropertyValue('--image-crop-ratio').trim();
                const cropRatio = parseFloat(cssRatio) || 0.5;

                function setCropForImage(img){
                    const parent = img.closest('.page-image-crop');
                    if(!parent) return;
                    const renderedWidth = img.clientWidth || img.width;
                    const naturalWidth = img.naturalWidth || renderedWidth;
                    const naturalHeight = img.naturalHeight || (renderedWidth * 1.5);
                    const renderedHeight = (renderedWidth / naturalWidth) * naturalHeight;
                    const visibleHeight = renderedHeight * cropRatio;
                    parent.style.height = visibleHeight + 'px';
                }

                function updateAll(){
                    document.querySelectorAll('.page-image-crop img').forEach(img => {
                        if(img.complete){
                            setCropForImage(img);
                        } else {
                            img.addEventListener('load', function onload(){
                                img.removeEventListener('load', onload);
                                setCropForImage(img);
                            });
                        }
                    });
                }

                window.addEventListener('resize', () => requestAnimationFrame(updateAll));
                document.addEventListener('DOMContentLoaded', updateAll);
                updateAll();
            })();
        </script>
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
