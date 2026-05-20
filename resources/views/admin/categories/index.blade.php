@extends('layouts.app')

@section('title', 'Category Management')

@section('content')
<div class="admin-category-management">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>📂 Category Management</h1>
        <div style="display: flex; gap: 1rem; align-items: center;">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">← Back to Dashboard</a>
            <a href="{{ route('admin.categories.create') }}" class="btn">+ Add Category</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Category Statistics -->
    <div class="category-stats" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
        <div class="card" style="background: linear-gradient(135deg, #2a2a2a 0%, #3a3a3a 100%);">
            <div class="card-body" style="text-align: center;">
                <div style="font-size: 2rem; margin-bottom: 0.5rem;">📂</div>
                <p style="font-size: 1.5rem; font-weight: bold; color: #ff6b6b; margin: 0;">{{ $categories->total() }}</p>
                <p style="color: #b0b0b0; margin: 0;">Total Categories</p>
            </div>
        </div>
        <div class="card" style="background: linear-gradient(135deg, #2a2a2a 0%, #3a3a3a 100%);">
            <div class="card-body" style="text-align: center;">
                <div style="font-size: 2rem; margin-bottom: 0.5rem;">📚</div>
                <p style="font-size: 1.5rem; font-weight: bold; color: #ff6b6b; margin: 0;">{{ $categories->getCollection()->sum('comics_count') }}</p>
                <p style="color: #b0b0b0; margin: 0;">Total Comics</p>
            </div>
        </div>
        <div class="card" style="background: linear-gradient(135deg, #2a2a2a 0%, #3a3a3a 100%);">
            <div class="card-body" style="text-align: center;">
                <div style="font-size: 2rem; margin-bottom: 0.5rem;">📊</div>
                <p style="font-size: 1.5rem; font-weight: bold; color: #ff6b6b; margin: 0;">{{ $categories->getCollection()->where('comics_count', '>', 0)->count() }}</p>
                <p style="color: #b0b0b0; margin: 0;">Active Categories</p>
            </div>
        </div>
    </div>

    <!-- Categories Table -->
    <div class="card">
        <div class="card-body">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="color: #ff6b6b;">All Categories</h3>
                <div style="display: flex; gap: 1rem; align-items: center;">
                    <input type="text" placeholder="Search categories..." id="categorySearch" style="padding: 0.5rem; background-color: #3a3a3a; color: #e0e0e0; border: 1px solid #4a4a4a; border-radius: 5px;">
                </div>
            </div>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #2a2a2a; border-bottom: 2px solid #3a3a3a;">
                            <th style="padding: 1rem; text-align: left; color: #ff6b6b;">Category</th>
                            <th style="padding: 1rem; text-align: left; color: #ff6b6b;">Slug</th>
                            <th style="padding: 1rem; text-align: center; color: #ff6b6b;">Comics</th>
                            <th style="padding: 1rem; text-align: left; color: #ff6b6b;">Description</th>
                            <th style="padding: 1rem; text-align: left; color: #ff6b6b;">Created</th>
                            <th style="padding: 1rem; text-align: center; color: #ff6b6b;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $category)
                            <tr class="category-row" style="border-bottom: 1px solid #3a3a3a;" onmouseover="this.style.backgroundColor='#2a2a2a'" onmouseout="this.style.backgroundColor='transparent'">
                                <td style="padding: 1rem;">
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <div style="width: 40px; height: 40px; border-radius: 8px; background: linear-gradient(135deg, #ff6b6b, #ff5252); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                            {{ strtoupper(substr($category->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div style="font-weight: bold; color: #e0e0e0;">{{ $category->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 1rem; color: #b0b0b0; font-family: monospace; font-size: 0.9rem;">{{ $category->slug }}</td>
                                <td style="padding: 1rem; text-align: center;">
                                    <span style="background: #4a4a4a; color: white; padding: 0.25rem 0.5rem; border-radius: 3px; font-size: 0.8rem;">
                                        {{ $category->comics_count }}
                                    </span>
                                </td>
                                <td style="padding: 1rem; color: #b0b0b0; font-size: 0.9rem; max-width: 300px;">
                                    {{ Str::limit($category->description ?? 'No description', 50) }}
                                </td>
                                <td style="padding: 1rem; color: #b0b0b0; font-size: 0.9rem;">{{ $category->created_at ? $category->created_at->format('M d, Y') : 'N/A' }}</td>
                                <td style="padding: 1rem; text-align: center;">
                                    <div style="display: flex; gap: 0.5rem; justify-content: center;">
                                        <a href="{{ route('categories.show', $category->slug) }}" target="_blank" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">View</a>
                                        <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">Edit</a>
                                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" onclick="return confirm('Are you sure you want to delete this category? This will remove it from all comics.')">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    @if($categories->hasPages())
        <div style="margin-top: 2rem; display: flex; justify-content: center;">
            {{ $categories->links() }}
        </div>
    @endif
</div>

<script>
// Search functionality
document.getElementById('categorySearch')?.addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('.category-row');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});
</script>

<style>
.category-stats .card {
    border-left: 4px solid #ff6b6b;
}

.category-row td {
    vertical-align: middle;
}

@media (max-width: 768px) {
    .category-stats {
        grid-template-columns: repeat(2, 1fr);
    }
    
    table {
        font-size: 0.8rem;
    }
    
    th, td {
        padding: 0.5rem !important;
    }
}
</style>
@endsection
