@extends('layouts.app')

@section('title', $comic->title)

@section('content')
    <style>
        @media (max-width: 768px) {
            .comic-header-grid {
                grid-template-columns: 1fr !important;
                gap: 1.5rem !important;
            }
            .comic-header-grid .poster-container img {
                width: 100% !important;
                max-width: 100% !important;
            }
        }
    </style>
    <div class="comic-header-grid" style="display: grid; grid-template-columns: 250px 1fr; gap: 2rem; margin-bottom: 2rem;">
        <div class="poster-container">
            @if($comic->poster)
                <img src="{{ $comic->poster }}" alt="{{ $comic->title }}" style="width: 100%; border-radius: 8px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);">
            @else
                <div style="width: 100%; height: 350px; background-color: #3a3a3a; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                    <span style="color: #999;">No image</span>
                </div>
            @endif
        </div>

        <div>
            <h1>{{ $comic->title }}</h1>
            <span class="badge badge-{{ $comic->status }}" style="display: block; width: fit-content;">{{ ucfirst($comic->status) }}</span>
            <p style="margin-top: 1rem; color: #b0b0b0;">
                <strong>Last Update:</strong> 
                @if($comic->latest_update)
                    {{ $comic->latest_update->diffForHumans() }}
                @else
                    Never
                @endif
            </p>
            <p style="margin-top: 1rem; font-size: 1.05rem; line-height: 1.8;">{{ $comic->description }}</p>
            
            @auth
                <div style="margin-top: 2rem; display: flex; gap: 1rem;">
                    <form action="{{ route('comics.bookmark', $comic) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-secondary">
                            @if(auth()->user()->bookmarkedComics()->where('comic_id', $comic->id)->exists())
                                ❌ Remove Bookmark
                            @else
                                🔖 Add Bookmark
                            @endif
                        </button>
                    </form>
                    @if(auth()->user()->isSuperAdmin())
                        <a href="{{ route('comics.edit', $comic) }}" class="btn">Edit Comic</a>
                        <form action="{{ route('comics.destroy', $comic) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-secondary" onclick="return confirm('Delete this comic and all its chapters?')">Delete Comic</button>
                        </form>
                    @endif
                </div>
            @endauth
        </div>
    </div>

    <div>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2>Chapters ({{ $comic->chapters->count() }})</h2>
            @auth
                @if(auth()->user()->isSuperAdmin())
                    <a href="{{ route('chapters.create', $comic) }}" class="btn">+ Add Chapter</a>
                @endif
            @endauth
        </div>

        @if($comic->chapters->count() > 0)
            <div class="chapter-list">
                @foreach($comic->chapters->sortByDesc('number') as $chapter)
                    <div class="chapter-item">
                        <div class="chapter-info">
                            <h3>Chapter {{ $chapter->number }}
                                @if($chapter->name) - {{ $chapter->name }} @endif
                            </h3>
                            @if($chapter->comment)
                                <p style="margin-top: 0.5rem; font-style: italic; color: #999;">{{ Str::limit($chapter->comment, 100) }}</p>
                            @endif
                        </div>
                        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; justify-content: flex-end;">
                            <a href="{{ route('chapters.show', [$comic, $chapter]) }}" class="btn" style="padding: 0.5rem 1rem;">Read</a>
                            @auth
                                @if(auth()->user()->isSuperAdmin())
                                    <a href="{{ route('chapters.edit', [$comic, $chapter]) }}" class="btn btn-secondary" style="padding: 0.5rem 1rem;">Edit</a>
                                    <form action="{{ route('chapters.destroy', [$comic, $chapter]) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-secondary" style="padding: 0.5rem 1rem;" onclick="return confirm('Delete this chapter?')">Delete</button>
                                    </form>
                                @endif
                            @endauth
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p style="text-align: center; color: #999; padding: 2rem;">No chapters yet. @auth <a href="{{ route('chapters.create', $comic) }}">Create the first one</a>@endauth</p>
        @endif
    </div>

    <x-disqus
        :url="url()->current()"
        :identifier="'comic-'.$comic->id"
    />
@endsection
