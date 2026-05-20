@extends('layouts.app')

@section('title', $comic->title . ' - Chapter ' . $chapter->number . ' - Read Online')

@section('description', 'Read ' . $comic->title . ' Chapter ' . $chapter->number . ' online. ' . ($chapter->name ? $chapter->name . '. ' : '') . 'High quality images and fast loading.')

@section('keywords', $comic->title . ', chapter ' . $chapter->number . ', manhwa, manga, comics, webtoons, read online')

@section('canonical', route('chapters.show', [$comic, $chapter]))

@section('og:type', 'article')

@section('og:url', route('chapters.show', [$comic, $chapter]))

@section('og:title', $comic->title . ' - Chapter ' . $chapter->number)

@section('og:description', 'Read ' . $comic->title . ' Chapter ' . $chapter->number . ' online. ' . ($chapter->name ? $chapter->name . '. ' : '') . 'High quality images and fast loading.')

@section('og:image', $comic->poster ?? asset('/images/og-default.jpg'))

@section('twitter:card', 'summary_large_image')

@section('twitter:url', route('chapters.show', [$comic, $chapter]))

@section('twitter:title', $comic->title . ' - Chapter ' . $chapter->number)

@section('twitter:description', 'Read ' . $comic->title . ' Chapter ' . $chapter->number . ' online. ' . ($chapter->name ? $chapter->name . '. ' : '') . 'High quality images and fast loading.')

@section('twitter:image', $comic->poster ?? asset('/images/og-default.jpg'))

@php
    // SEO: Generate JSON-LD structured data for chapter page
    $jsonLd = [
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => $comic->title . ' - Chapter ' . $chapter->number,
        'description' => 'Read ' . $comic->title . ' Chapter ' . $chapter->number . ' online. ' . ($chapter->name ? $chapter->name . '. ' : ''),
        'author' => [
            '@type' => 'Organization',
            'name' => 'Manhwa Website'
        ],
        'url' => route('chapters.show', [$comic, $chapter]),
        'image' => $comic->poster ?? asset('/images/og-default.jpg'),
        'datePublished' => $chapter->created_at->toIso8601String(),
        'dateModified' => $chapter->updated_at->toIso8601String(),
        'isPartOf' => [
            '@type' => 'Book',
            'name' => $comic->title,
            'url' => route('comics.show', $comic)
        ],
        'position' => $chapter->number
    ];
@endphp

@section('json-ld')
<script type="application/ld+json">
{!! json_encode($jsonLd) !!}
</script>
@endsection

@section('content')
    <div class="reader-container">
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 2rem;">
            <div>
                <a href="{{ route('comics.show', $comic) }}"><h2>{{ $comic->title }}</h2></a>
                <p style="color: #b0b0b0;">Chapter {{ $chapter->number }}
                    @if($chapter->name) - {{ $chapter->name }} @endif
                </p>
            </div>
            
            @auth
                @if(auth()->user()->isSuperAdmin())
                <div style="display: flex; gap: 0.5rem;">
                    <a href="{{ route('chapters.edit', [$comic, $chapter]) }}" class="btn btn-secondary" style="padding: 0.5rem 1rem;">Edit</a>
                    <form action="{{ route('chapters.destroy', [$comic, $chapter]) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-secondary" style="padding: 0.5rem 1rem;" onclick="return confirm('Delete this chapter?')">Delete</button>
                    </form>
                </div>
                @endif
            @endauth
        </div>


        {{-- Prev/Next buttons (top) --}}
            <div style="display:flex; gap:0.5rem; align-items:center;">
                @php
                    $prevChapter = $comic->chapters()->where('number', '<', $chapter->number)->orderBy('number', 'desc')->first();
                    $nextChapter = $comic->chapters()->where('number', '>', $chapter->number)->orderBy('number', 'asc')->first();
                    
                @endphp
                @if($prevChapter)
                    <a href="{{ route('chapters.show', [$comic, $prevChapter]) }}" class="btn btn-secondary">← Prev</a>
                @endif
                @if($nextChapter)
                    <a href="{{ route('chapters.show', [$comic, $nextChapter]) }}" class="btn btn-secondary">Next →</a>
                @endif
            </div>

        <div class="pages-container" data-image-crop-ratio="0.5">
            @php
                use Illuminate\Support\Facades\Storage;
            @endphp
            @foreach ($chapter->images as $img)
                <div class="page-image-crop">
                    <img loading="lazy" src="{{ $img->path }}" alt="{{ $img->alt ?? 'Page '.$img->page_number }}">
                </div>
            @endforeach
        </div>

        {{-- Prev/Next buttons (bottom) --}}
        <div style="display:flex; gap:0.5rem; justify-content:center; margin-top:1rem; margin-bottom:1rem;">
            @if(isset($prevChapter) && $prevChapter)
                <a href="{{ route('chapters.show', [$comic, $prevChapter]) }}" class="btn btn-secondary">← Prev</a>
            @endif
            @if(isset($nextChapter) && $nextChapter)
                <a href="{{ route('chapters.show', [$comic, $nextChapter]) }}" class="btn btn-secondary">Next →</a>
            @endif
        </div>

        @auth
            <div style="background-color: #3a3a3a; padding: 2rem; border-radius: 8px; margin-top: 2rem;">
                {{-- Comment section removed - to be modified later --}}
            </div>
        @endauth

        <div style="display: flex; justify-content: space-between; gap: 1rem; margin-top: 2rem;">
            <a href="{{ route('comics.show', $comic) }}" class="btn btn-secondary">← Back to Comic</a>
        </div>
        <!-- Comment Section -->
        <div style="margin-top: 3rem;">
            <x-disqus
            :url="url()->current()"
            :identifier="'chapter-'.$chapter->id"
        />
        </div>
    </div>
    </div>
@endsection
