@extends('layouts.app')

@section('title', 'Edit Comic')

@section('content')
    <h1>Edit Comic: {{ $comic->title }}</h1>

    <form action="{{ route('comics.update', $comic) }}" method="POST" enctype="multipart/form-data" style="max-width: 600px;">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="title">Title *</label>
            <input type="text" id="title" name="title" value="{{ old('title', $comic->title) }}" required>
            @error('title')
                <p style="color: #f44336; font-size: 0.9rem;">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description">{{ old('description', $comic->description) }}</textarea>
            @error('description')
                <p style="color: #f44336; font-size: 0.9rem;">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="poster">Poster Image</label>
            @if($comic->poster)
                <div style="margin-bottom: 1rem;">
                    <img src="{{ $comic->poster }}" alt="{{ $comic->title }}" style="max-width: 200px; border-radius: 5px;">
                </div>
            @endif
            <input type="file" id="poster" name="poster" accept="image/*">
            @error('poster')
                <p style="color: #f44336; font-size: 0.9rem;">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="status">Status *</label>
            <select id="status" name="status" required>
                <option value="">Select status...</option>
                <option value="ongoing" @selected(old('status', $comic->status) == 'ongoing')>Ongoing</option>
                <option value="completed" @selected(old('status', $comic->status) == 'completed')>Completed</option>
                <option value="hiatus" @selected(old('status', $comic->status) == 'hiatus')>Hiatus</option>
            </select>
            @error('status')
                <p style="color: #f44336; font-size: 0.9rem;">{{ $message }}</p>
            @enderror
        </div>

        <div style="display: flex; gap: 1rem;">
            <button type="submit" class="btn">Update Comic</button>
            <a href="{{ route('comics.show', $comic) }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
@endsection
