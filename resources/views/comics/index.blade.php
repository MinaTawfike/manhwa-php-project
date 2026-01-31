@extends('layouts.app')

@section('title', 'Comics')

@section('content')
    <div>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1>Manhwa Comics</h1>
            @auth
                @if(auth()->user()->isSuperAdmin())
                    <a href="{{ route('comics.create') }}" class="btn">+ Add Comic</a>
                @endif
            @endauth
        </div>

        <div class="grid">
            @forelse($comics as $comic)
                <div class="card">
                    @if($comic->poster)
                        <img src="{{ $comic->poster }}" alt="{{ $comic->title }}">
                    @else
                        <div style="width: 100%; height: 250px; background-color: #3a3a3a; display: flex; align-items: center; justify-content: center;">
                            <span style="color: #999;">No image</span>
                        </div>
                    @endif
                    <div class="card-body">
                        <h3 class="card-title">{{ $comic->title }}</h3>
                        <span class="badge badge-{{ $comic->status }}">{{ ucfirst($comic->status) }}</span>
                        <p class="card-text">{{ Str::limit($comic->description, 100) }}</p>
                        <div style="display: flex; gap: 0.5rem;">
                            <a href="{{ route('comics.show', $comic) }}" class="btn" style="flex: 1; text-align: center; padding: 0.5rem;">View</a>
                            @auth
                                @if(auth()->user()->isSuperAdmin())
                                    <a href="{{ route('comics.edit', $comic) }}" class="btn btn-secondary" style="flex: 1; text-align: center; padding: 0.5rem;">Edit</a>
                                    <form action="{{ route('comics.destroy', $comic) }}" method="POST" style="flex: 1;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-secondary" style="width: 100%;" onclick="return confirm('Delete this comic?')">Delete</button>
                                    </form>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
            @empty
                <p style="grid-column: 1 / -1; text-align: center;">No comics found.</p>
            @endforelse
        </div>

        <div style="margin-top: 2rem;">
            {{ $comics->links() }}
        </div>
    </div>
@endsection
