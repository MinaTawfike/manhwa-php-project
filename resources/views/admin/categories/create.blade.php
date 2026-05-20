@extends('layouts.app')

@section('title', 'Create Category')

@section('content')
<div class="admin-category-create">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>📂 Create Category</h1>
        <div style="display: flex; gap: 1rem; align-items: center;">
            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">← Back to Categories</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label for="name">Category Name *</label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        required
                        placeholder="e.g., Action, Romance, Fantasy"
                        value="{{ old('name') }}"
                    >
                    @error('name')
                        <div style="color: #ff6b6b; margin-top: 0.5rem; font-size: 0.9rem;">{{ $message }}</div>
                    @enderror
                    <p style="color: #999; font-size: 0.8rem; margin-top: 0.5rem;">The slug will be automatically generated from the name.</p>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea 
                        id="description" 
                        name="description" 
                        rows="4"
                        placeholder="Describe this category for users..."
                    >{{ old('description') }}</textarea>
                    @error('description')
                        <div style="color: #ff6b6b; margin-top: 0.5rem; font-size: 0.9rem;">{{ $message }}</div>
                    @enderror
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="submit" class="btn">Create Category</button>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.form-group {
    margin-bottom: 1.5rem;
}

label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: bold;
    color: #e0e0e0;
}

input[type="text"],
textarea {
    width: 100%;
    padding: 0.8rem;
    background-color: #3a3a3a;
    color: #e0e0e0;
    border: 1px solid #4a4a4a;
    border-radius: 5px;
    font-size: 1rem;
}

input[type="text"]:focus,
textarea:focus {
    outline: none;
    border-color: #ff6b6b;
}

textarea {
    resize: vertical;
    min-height: 120px;
}
</style>
@endsection
