@extends('layouts.app')

@section('title', 'Edit Category')

@section('content')
<div class="admin-category-edit">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>📂 Edit Category</h1>
        <div style="display: flex; gap: 1rem; align-items: center;">
            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">← Back to Categories</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.categories.update', $category) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="form-group">
                    <label for="name">Category Name *</label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        required
                        placeholder="e.g., Action, Romance, Fantasy"
                        value="{{ old('name', $category->name) }}"
                    >
                    @error('name')
                        <div style="color: #ff6b6b; margin-top: 0.5rem; font-size: 0.9rem;">{{ $message }}</div>
                    @enderror
                    <p style="color: #999; font-size: 0.8rem; margin-top: 0.5rem;">Current slug: <code>{{ $category->slug }}</code> (will be regenerated if name changes)</p>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea 
                        id="description" 
                        name="description" 
                        rows="4"
                        placeholder="Describe this category for users..."
                    >{{ old('description', $category->description) }}</textarea>
                    @error('description')
                        <div style="color: #ff6b6b; margin-top: 0.5rem; font-size: 0.9rem;">{{ $message }}</div>
                    @enderror
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="submit" class="btn">Update Category</button>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Category Info -->
    <div class="card" style="margin-top: 2rem;">
        <div class="card-body">
            <h3 style="color: #ff6b6b; margin-bottom: 1rem;">Category Information</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                <div>
                    <span style="color: #999; font-size: 0.8rem;">ID:</span>
                    <span style="color: #e0e0e0; margin-left: 0.5rem;">{{ $category->id }}</span>
                </div>
                <div>
                    <span style="color: #999; font-size: 0.8rem;">Slug:</span>
                    <span style="color: #e0e0e0; margin-left: 0.5rem; font-family: monospace;">{{ $category->slug }}</span>
                </div>
                <div>
                    <span style="color: #999; font-size: 0.8rem;">Comics:</span>
                    <span style="color: #e0e0e0; margin-left: 0.5rem;">{{ $category->comics->count() }}</span>
                </div>
                <div>
                    <span style="color: #999; font-size: 0.8rem;">Created:</span>
                    <span style="color: #e0e0e0; margin-left: 0.5rem;">{{ $category->created_at->format('M d, Y') }}</span>
                </div>
                <div>
                    <span style="color: #999; font-size: 0.8rem;">Updated:</span>
                    <span style="color: #e0e0e0; margin-left: 0.5rem;">{{ $category->updated_at->format('M d, Y') }}</span>
                </div>
            </div>
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

code {
    background: #3a3a3a;
    padding: 0.2rem 0.4rem;
    border-radius: 3px;
    font-family: monospace;
}
</style>
@endsection
