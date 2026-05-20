@extends('layouts.app')

@section('title', 'Create Comic')

@section('content')
    <h1>Create New Comic</h1>

    <form action="{{ route('comics.store') }}" method="POST" enctype="multipart/form-data" style="max-width: 600px;">
        @csrf

        <div class="form-group">
            <label for="title">Title *</label>
            <input type="text" id="title" name="title" value="{{ old('title') }}" required>
            @error('title')
                <p style="color: #f44336; font-size: 0.9rem;">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description">{{ old('description') }}</textarea>
            @error('description')
                <p style="color: #f44336; font-size: 0.9rem;">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="poster">Poster Image</label>
            <input type="file" id="poster" name="poster" accept="image/*">
            @error('poster')
                <p style="color: #f44336; font-size: 0.9rem;">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="status">Status *</label>
            <select id="status" name="status" required>
                <option value="">Select status...</option>
                <option value="ongoing" @selected(old('status') == 'ongoing')>Ongoing</option>
                <option value="completed" @selected(old('status') == 'completed')>Completed</option>
                <option value="hiatus" @selected(old('status') == 'hiatus')>Hiatus</option>
            </select>
            @error('status')
                <p style="color: #f44336; font-size: 0.9rem;">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="categories">Categories</label>
            <select id="categories" name="categories[]" multiple style="height: 150px;">
                @foreach($categories ?? [] as $category)
                    <option value="{{ $category->id }}" @if(in_array($category->id, old('categories') ?? [])) selected @endif>{{ $category->name }}</option>
                @endforeach
            </select>
            <p style="color: #999; font-size: 0.8rem; margin-top: 0.5rem;">Hold Ctrl/Cmd to select multiple categories</p>
            @error('categories')
                <p style="color: #f44336; font-size: 0.9rem;">{{ $message }}</p>
            @enderror
        </div>

        <div style="display: flex; gap: 1rem;">
            <button type="submit" class="btn">Create Comic</button>
            <a href="{{ route('comics.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
@endsection
