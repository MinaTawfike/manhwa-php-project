@extends('layouts.app')

@section('title', $category->name . ' - Manhwa Comics')

@section('description', 'Browse ' . $category->name . ' manhwa comics. ' . ($category->description ?: 'Read the best ' . $category->name . ' comics online.'))

@section('keywords', $category->name . ', manhwa, manga, comics, webtoons, read online')

@section('canonical', route('categories.show', $category))

@section('og:type', 'website')

@section('og:url', route('categories.show', $category))

@section('og:title', $category->name . ' - Manhwa Comics')

@section('og:description', 'Browse ' . $category->name . ' manhwa comics. ' . ($category->description ?: 'Read the best ' . $category->name . ' comics online.'))

@section('og:image', asset('/images/og-default.jpg'))

@section('twitter:card', 'summary_large_image')

@section('twitter:url', route('categories.show', $category))

@section('twitter:title', $category->name . ' - Manhwa Comics')

@section('twitter:description', 'Browse ' . $category->name . ' manhwa comics. ' . ($category->description ?: 'Read the best ' . $category->name . ' comics online.'))

@section('twitter:image', asset('/images/og-default.jpg'))

@section('content')
<div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>{{ ucwords($category->name) }}</h1>
        <a href="{{ route('comics.index') }}" class="btn btn-secondary">← All Comics</a>
    </div>

    @if($category->description)
        <p style="margin-bottom: 2rem; color: #b0b0b0; font-size: 1.1rem;">{{ $category->description }}</p>
    @endif

    <div style="margin-bottom: 1rem; color: #999;">
        {{ $comics->total() }} comics found
    </div>

    <div class="grid">
        @forelse($comics as $comic)
            <div class="card">
                @if($comic->poster)
                    <img src="{{ $comic->poster }}" alt="{{ $comic->title }} - {{ ucfirst($comic->status) }} manhwa comic cover" loading="lazy" width="250" height="350">
                @else
                    <div style="width: 100%; height: 250px; background-color: #3a3a3a; display: flex; align-items: center; justify-content: center;">
                        <span style="color: #999;">No image</span>
                    </div>
                @endif
                <div class="card-body">
                    <h3 class="card-title">{{ $comic->title }}</h3>
                    <span class="badge badge-{{ $comic->status }}">{{ ucfirst($comic->status) }}</span>
                    <p class="card-text">{{ Str::limit($comic->description, 100) }}</p>
                    @if($comic->categories->count() > 0)
                        <div style="margin: 0.5rem 0;">
                            @foreach($comic->categories as $cat)
                                <a href="{{ route('categories.show', $cat) }}" style="color: #ff6b6b; font-size: 0.8rem; text-decoration: none;">{{ $cat->name }}</a>{{ !$loop->last ? ', ' : '' }}
                            @endforeach
                        </div>
                    @endif
                    @if($comic->chapters->count() > 0)
                        <p style="margin: 0 0 0.5rem 0; font-size: 0.9rem; color: #b0b0b0;">
                            <strong>Latest:</strong> Chapter {{ $comic->chapters->sortByDesc('number')->first()->number }}
                        </p>
                    @endif
                    <a href="{{ route('comics.show', $comic) }}" class="btn" style="text-align: center; padding: 0.5rem;">View</a>
                </div>
            </div>
        @empty
            <p style="grid-column: 1 / -1; text-align: center; color: #999; padding: 2rem;">No comics found in this category.</p>
        @endforelse
    </div>

    @if($comics->hasPages())
        <div style="margin-top: 2rem; display: flex; justify-content: center;">
            {{ $comics->links() }}
        </div>
    @endif
</div>
@endsection
