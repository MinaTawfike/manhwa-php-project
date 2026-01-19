@extends('layouts.app')

@section('title', 'Create Chapter')

@section('content')
    <h1>Create New Chapter - {{ $comic->title }}</h1>
    {{-- Show validation errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Important: enctype must be multipart/form-data for file upload --}}

    <form action="{{ route('chapters.store', $comic) }}" method="POST" style="max-width: 600px;" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label for="number">Chapter Number *</label>
            <input type="number" id="number" name="number" value="{{ old('number') }}" required>
            @error('number')
                <p style="color: #f44336; font-size: 0.9rem;">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="name">Chapter Name</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Optional chapter title">
            @error('name')
                <p style="color: #f44336; font-size: 0.9rem;">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="comment">Comment</label>
            <textarea id="comment" name="comment" placeholder="Add a comment about this chapter...">{{ old('comment') }}</textarea>
            @error('comment')
                <p style="color: #f44336; font-size: 0.9rem;">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Multiple images upload -->
        <div class="form-group">
            <label for="images" class="form-label">Pages (you can select multiple)</label>
            <input id="images" name="images[]" type="file" class="form-control" accept="image/*" multiple>
            <small class="text-muted">Allowed: jpeg, jpg, png, webp, avif. Each up to ~5MB.</small>
        </div>

        <div style="display: flex; gap: 1rem;">
            <button type="submit" class="btn">Create Chapter</button>
            <a href="{{ route('comics.show', $comic) }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
@endsection
