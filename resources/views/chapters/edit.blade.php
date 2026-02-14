@extends('layouts.app')

@section('title', 'Edit Chapter')

@section('content')
    <h1>Edit Chapter {{ $chapter->number }} - {{ $comic->title }}</h1>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

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


    <form action="{{ route('chapters.update', [$comic, $chapter]) }}" method="POST" style="max-width: 600px;" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="number">Chapter Number *</label>
            <input type="number" id="number" name="number" value="{{ old('number', $chapter->number) }}" required>
            @error('number')
                <p style="color: #f44336; font-size: 0.9rem;">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="name">Chapter Name</label>
            <input type="text" id="name" name="name" value="{{ old('name', $chapter->name) }}" placeholder="Optional chapter title">
            @error('name')
                <p style="color: #f44336; font-size: 0.9rem;">{{ $message }}</p>
            @enderror
        </div>

        <!-- New images to upload -->
        <div class="form-group">
            <label for="images" class="form-label">Add more pages</label>
            <input id="images" name="images[]" type="file" class="form-control" accept="image/*" multiple>
            <small class="text-muted">You can upload multiple images to append to the end.</small>
        </div>

        <!-- Existing pages: reorder and delete -->
        <div class="form-group">
            <label class="form-label">Existing pages</label>

            <div class="row g-3">
                @foreach ($chapter->images as $img)
                    <div class="col-12 d-flex align-items-start border p-2 rounded">
                        {{-- Thumbnail (cropped to top using object-position) --}}
                        <img src="{{ $img->path }}"
                             alt="{{ $img->alt ?? 'Page '.$img->page_number }}"
                             style="width: 120px; height: 120px; object-fit: cover; object-position: top; margin-right: 12px;">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center mb-2">
                                <!-- Page number input for reordering -->
                                <label class="me-2">Page #</label>
                                <input type="number"
                                       name="order[{{ $img->id }}]"
                                       value="{{ $img->page_number }}"
                                       min="1"
                                       class="form-control"
                                       style="width: 100px;">
                            </div>
                            <div class="form-check">
                                <input class="form-check-input"
                                       type="checkbox"
                                       name="delete_images[]"
                                       value="{{ $img->id }}"
                                       id="del_{{ $img->id }}">
                                <label class="form-check-label" for="del_{{ $img->id }}">Delete this page</label>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <small class="text-muted d-block mt-2">Adjust page numbers to reorder. Check delete to remove specific images.</small>
        </div>


        <div class="form-group">
            <label for="comment">Comment</label>
            <textarea id="comment" name="comment" placeholder="Add a comment about this chapter...">{{ old('comment', $chapter->comment) }}</textarea>
            @error('comment')
                <p style="color: #f44336; font-size: 0.9rem;">{{ $message }}</p>
            @enderror
        </div>

        <div style="display: flex; gap: 1rem;">
            <button type="submit" class="btn">Update Chapter</button>
            <a href="{{ route('chapters.show', [$comic, $chapter]) }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
@endsection
