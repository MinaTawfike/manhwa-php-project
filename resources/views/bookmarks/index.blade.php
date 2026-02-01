@extends('layouts.app')

@section('title', 'My Bookmarks')

@section('content')
    <div class="reader-container">
        <h1 style="margin-bottom: 2rem;">My Bookmarks</h1>

        @if($bookmarkedComics->count() > 0)
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 2rem; margin-bottom: 2rem;">
                @foreach($bookmarkedComics as $comic)
                    <div style="background-color: #2a2a2a; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);">
                        {{-- Comic Poster --}}
                        <div style="height: 300px; overflow: hidden; background-color: #1a1a1a;">
                            @if($comic->poster)
                                <img src="{{ $comic->poster }}" alt="{{ $comic->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">
                                    <span style="color: #999;">No image</span>
                                </div>
                            @endif
                        </div>

                        {{-- Comic Info --}}
                        <div style="padding: 1rem;">
                            <h3 style="margin: 0 0 0.5rem 0; font-size: 1.1rem;">{{ $comic->title }}</h3>
                            <span class="badge badge-{{ $comic->status }}" style="display: inline-block; font-size: 0.85rem; margin-bottom: 1rem;">{{ ucfirst($comic->status) }}</span>

                            {{-- Last Chapter Info --}}
                            <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #444;">
                                @php
                                    $lastChapterRecord = $comic->userLastChapters->first();
                                    $lastChapter = $lastChapterRecord ? $lastChapterRecord->chapter : null;
                                    $latestChapter = $comic->chapters->sortByDesc('number')->first();
                                    $hasUnread = $latestChapter && (!$lastChapter || $lastChapter->id !== $latestChapter->id);
                                @endphp

                                {{-- Last Viewed --}}
                                @if($lastChapter)
                                    <p style="margin: 0 0 0.5rem 0; font-size: 0.9rem; color: #b0b0b0; display: flex; align-items: center; gap: 0.5rem;">
                                        <strong>Last Viewed:</strong> Chapter {{ $lastChapter->number }}
                                        
                                        @if($lastChapter->name)
                                            - {{ $lastChapter->name }}
                                        @endif
                                    </p>
                                @else
                                    <p style="margin: 0 0 0.5rem 0; font-size: 0.9rem; color: #999;">
                                        <strong>Last Viewed:</strong> Not started yet
                                    </p>
                                @endif

                                {{-- Latest Available --}}
                                @if($latestChapter)
                                    <p style="margin: 0 0 0.5rem 0; font-size: 0.9rem; color: #b0b0b0;">
                                        <strong>Latest Available:</strong> Chapter {{ $latestChapter->number }}
                                        @if($hasUnread)
                                            <span style="width: 10px; height: 10px; background-color: #4caf50; border-radius: 50%; display: inline-block;" title="Unread chapters available"></span>
                                        @endif
                                        @if($latestChapter->name)
                                            - {{ $latestChapter->name }}
                                        @endif
                                    </p>
                                @endif

                                {{-- Continue Reading Button --}}
                                @if($lastChapter)
                                    <a href="{{ route('chapters.show', [$comic, $lastChapter]) }}" class="btn" style="display: inline-block; padding: 0.5rem 1rem; font-size: 0.9rem; width: 100%; text-align: center;">Continue Reading</a>
                                @else
                                    <p style="margin: 0; font-size: 0.9rem; color: #999; text-align: center;">No chapters viewed yet</p>
                                    @if($comic->chapters->count() > 0)
                                        @php
                                            $firstChapter = $comic->chapters->sortBy('number')->first();
                                        @endphp
                                        <a href="{{ route('chapters.show', [$comic, $firstChapter]) }}" class="btn" style="display: inline-block; padding: 0.5rem 1rem; font-size: 0.9rem; width: 100%; text-align: center; margin-top: 0.5rem;">Start Reading</a>
                                    @endif
                                @endif
                            </div>

                            {{-- View Comic Link --}}
                            <a href="{{ route('comics.show', $comic) }}" style="display: inline-block; margin-top: 0.5rem; font-size: 0.9rem; color: #ff6b6b; text-decoration: none;">View Comic →</a>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div style="margin-top: 2rem;">
                {{ $bookmarkedComics->links() }}
            </div>
        @else
            <div style="text-align: center; padding: 3rem; background-color: #2a2a2a; border-radius: 8px;">
                <p style="color: #999; font-size: 1.1rem; margin: 0;">No bookmarked comics yet.</p>
                <a href="{{ route('comics.index') }}" class="btn" style="display: inline-block; margin-top: 1rem;">Browse Comics</a>
            </div>
        @endif
    </div>
@endsection
