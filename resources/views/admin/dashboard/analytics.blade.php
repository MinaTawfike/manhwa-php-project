@extends('layouts.app')

@section('title', 'Comic Analytics')

@section('content')
<div class="admin-analytics">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>📊 Comic Analytics</h1>
        <div style="display: flex; gap: 1rem; align-items: center;">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">← Back to Dashboard</a>
            <a href="{{ route('admin.system-health') }}" class="btn btn-secondary">🔧 System Health</a>
        </div>
    </div>

    <!-- Filter Controls -->
    <div class="filter-controls" style="background: #2a2a2a; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h3 style="color: #ff6b6b; margin-bottom: 0.5rem;">Filter & Sort</h3>
                <p style="color: #b0b0b0; margin: 0; font-size: 0.9rem;">Currently showing: <strong>{{ ucfirst(str_replace('_', ' ', $filter)) }}</strong></p>
            </div>
            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                <a href="?filter=most_viewed" class="filter-btn {{ $filter == 'most_viewed' ? 'active' : '' }}" style="padding: 0.5rem 1rem; background-color: {{ $filter == 'most_viewed' ? '#ff6b6b' : '#4a4a4a' }}; color: white; text-decoration: none; border-radius: 5px; font-size: 0.9rem;">
                    📈 Most Viewed
                </a>
                <a href="?filter=recently_updated" class="filter-btn {{ $filter == 'recently_updated' ? 'active' : '' }}" style="padding: 0.5rem 1rem; background-color: {{ $filter == 'recently_updated' ? '#ff6b6b' : '#4a4a4a' }}; color: white; text-decoration: none; border-radius: 5px; font-size: 0.9rem;">
                    🕒 Recently Updated
                </a>
                <a href="?filter=most_bookmarked" class="filter-btn {{ $filter == 'most_bookmarked' ? 'active' : '' }}" style="padding: 0.5rem 1rem; background-color: {{ $filter == 'most_bookmarked' ? '#ff6b6b' : '#4a4a4a' }}; color: white; text-decoration: none; border-radius: 5px; font-size: 0.9rem;">
                    🔖 Most Bookmarked
                </a>
                <a href="?filter=newest" class="filter-btn {{ $filter == 'newest' ? 'active' : '' }}" style="padding: 0.5rem 1rem; background-color: {{ $filter == 'newest' ? '#ff6b6b' : '#4a4a4a' }}; color: white; text-decoration: none; border-radius: 5px; font-size: 0.9rem;">
                    ✨ Newest
                </a>
            </div>
        </div>
    </div>

    <!-- Analytics Summary -->
    <div class="analytics-summary" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
        <div class="card" style="background: linear-gradient(135deg, #2a2a2a 0%, #3a3a3a 100%);">
            <div class="card-body" style="text-align: center;">
                <div style="font-size: 2rem; margin-bottom: 0.5rem;">📚</div>
                <p style="font-size: 1.5rem; font-weight: bold; color: #ff6b6b; margin: 0;">{{ $comicAnalytics->total() }}</p>
                <p style="color: #b0b0b0; margin: 0;">Total Comics</p>
            </div>
        </div>
        <div class="card" style="background: linear-gradient(135deg, #2a2a2a 0%, #3a3a3a 100%);">
            <div class="card-body" style="text-align: center;">
                <div style="font-size: 2rem; margin-bottom: 0.5rem;">📖</div>
                <p style="font-size: 1.5rem; font-weight: bold; color: #ff6b6b; margin: 0;">{{ $comicAnalytics->getCollection()->sum(fn($comic) => $comic->chapters->count()) }}</p>
                <p style="color: #b0b0b0; margin: 0;">Total Chapters</p>
            </div>
        </div>
        <div class="card" style="background: linear-gradient(135deg, #2a2a2a 0%, #3a3a3a 100%);">
            <div class="card-body" style="text-align: center;">
                <div style="font-size: 2rem; margin-bottom: 0.5rem;">🔖</div>
                <p style="font-size: 1.5rem; font-weight: bold; color: #ff6b6b; margin: 0;">{{ $comicAnalytics->getCollection()->sum(fn($comic) => $comic->bookmarkedBy->count()) }}</p>
                <p style="color: #b0b0b0; margin: 0;">Total Bookmarks</p>
            </div>
        </div>
        
        <div class="card" style="background: linear-gradient(135deg, #2a2a2a 0%, #3a3a3a 100%);">
            <div class="card-body" style="text-align: center;">
                <div style="font-size: 2rem; margin-bottom: 0.5rem;">👥</div>
                <p style="font-size: 1.5rem; font-weight: bold; color: #ff6b6b; margin: 0;">{{ number_format($comicAnalytics->getCollection()->sum(fn($comic) => app(\App\Services\ViewTrackingService::class)->getComicUniqueViewCount($comic))) }}</p>
                <p style="color: #b0b0b0; margin: 0;">Unique Comic Views</p>
            </div>
        </div>
        <div class="card" style="background: linear-gradient(135deg, #2a2a2a 0%, #3a3a3a 100%);">
            <div class="card-body" style="text-align: center;">
                <div style="font-size: 2rem; margin-bottom: 0.5rem;">�️</div>
                <p style="font-size: 1.5rem; font-weight: bold; color: #ff6b6b; margin: 0;">{{ number_format($comicAnalytics->getCollection()->sum(fn($comic) => $comic->chapters->sum('views_count')) + $comicAnalytics->getCollection()->sum('views_count')) }}</p>
                <p style="color: #b0b0b0; margin: 0;">Total Views</p>
            </div>
        </div>
    </div>

    <!-- Comic Analytics List -->
    <div class="comic-analytics-list">
        @forelse($comicAnalytics as $index => $comic)
            <div class="comic-analytics-card" style="margin-bottom: 1rem;">
                <!-- Comic Header (Collapsible) -->
                <div class="comic-header" onclick="toggleComicDetails({{ $comic->id }})" style="background: #2a2a2a; border-radius: 8px; padding: 1.5rem; cursor: pointer; transition: all 0.3s; border-left: 4px solid #ff6b6b;" onmouseover="this.style.backgroundColor='#3a3a3a'" onmouseout="this.style.backgroundColor='#2a2a2a'">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div style="flex: 1;">
                            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.5rem;">
                                <span style="font-size: 1.2rem; color: #ff6b6b;">#{{ $comicAnalytics->firstItem() + $index }}</span>
                                <h3 style="color: #e0e0e0; margin: 0;">{{ $comic->title }}</h3>
                                <span class="badge badge-{{ $comic->status }}" style="font-size: 0.8rem;">{{ ucfirst($comic->status) }}</span>
                            </div>
                            <p style="color: #b0b0b0; margin: 0.5rem 0; font-size: 0.9rem;">{{ Str::limit($comic->description, 150) }}</p>
                            <div style="display: flex; gap: 2rem; flex-wrap: wrap; margin-top: 1rem;">
                                <div>
                                    <span style="color: #999; font-size: 0.8rem;">Chapters:</span>
                                    <span style="color: #e0e0e0; font-weight: bold; margin-left: 0.5rem;">{{ $comic->chapters->count() }}</span>
                                </div>
                                <div>
                                    <span style="color: #999; font-size: 0.8rem;">Bookmarks:</span>
                                    <span style="color: #e0e0e0; font-weight: bold; margin-left: 0.5rem;">{{ $comic->bookmarkedBy->count() }}</span>
                                </div>
                                <div>
                                    <span style="color: #999; font-size: 0.8rem;">Total Views:</span>
                                    <span style="color: #e0e0e0; margin-left: 0.5rem;">{{ number_format($comic->views_count) }}</span>
                                </div>
                                <div>
                                    <span style="color: #999; font-size: 0.8rem;">Total Chapter Views:</span>
                                    <span style="color: #e0e0e0; margin-left: 0.5rem;">{{ number_format($comic->chapters->sum('views_count')) }}</span>
                                </div>
                                <div>
                                    <span style="color: #999; font-size: 0.8rem;">Created:</span>
                                    <span style="color: #e0e0e0; margin-left: 0.5rem;">{{ $comic->created_at->format('M d, Y') }}</span>
                                </div>
                                @if($comic->latest_update)
                                    <div>
                                        <span style="color: #999; font-size: 0.8rem;">Updated:</span>
                                        <span style="color: #e0e0e0; margin-left: 0.5rem;">{{ $comic->latest_update->diffForHumans() }}</span>
                                    </div>
                                @endif
                                <div>
                                    <span style="color: #999; font-size: 0.8rem;">Author:</span>
                                    <span style="color: #e0e0e0; margin-left: 0.5rem;">{{ $comic->user?->name ?? 'Unknown' }}</span>
                                </div>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div style="text-align: right; margin-right: 1rem;">
                                <div style="font-size: 2rem; font-weight: bold; color: #ff6b6b;">📊</div>
                                <p style="color: #b0b0b0; margin: 0; font-size: 0.8rem;">Click to expand</p>
                            </div>
                            <div id="chevron-{{ $comic->id }}" style="font-size: 1.5rem; color: #ff6b6b; transition: transform 0.3s;">
                                ▼
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Comic Details (Collapsible Content) -->
                <div id="comic-details-{{ $comic->id }}" style="display: none; background: #1f1f1f; border-radius: 0 0 8px 8px; overflow: hidden;">
                    <!-- Detailed Stats -->
                    <div style="padding: 1.5rem; border-bottom: 1px solid #3a3a3a;">
                        <h4 style="color: #ff6b6b; margin-bottom: 1rem;">📈 Detailed Statistics</h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem;">
                            <div style="background: #2a2a2a; padding: 1rem; border-radius: 5px; text-align: center;">
                                <div style="font-size: 1.5rem; font-weight: bold; color: #ff6b6b;">{{ $comic->chapters->count() }}</div>
                                <div style="color: #b0b0b0; font-size: 0.8rem;">Total Chapters</div>
                            </div>
                            <div style="background: #2a2a2a; padding: 1rem; border-radius: 5px; text-align: center;">
                                <div style="font-size: 1.5rem; font-weight: bold; color: #ff6b6b;">{{ $comic->bookmarkedBy->count() }}</div>
                                <div style="color: #b0b0b0; font-size: 0.8rem;">Total Bookmarks</div>
                            </div>
                            <div style="background: #2a2a2a; padding: 1rem; border-radius: 5px; text-align: center;">
                                <div style="font-size: 1.5rem; font-weight: bold; color: #ff6b6b;">{{ number_format($comic->views_count) }}</div>
                                <div style="color: #b0b0b0; font-size: 0.8rem;">Total Views</div>
                            </div>
                            <div style="background: #2a2a2a; padding: 1rem; border-radius: 5px; text-align: center;">
                                <div style="font-size: 1.5rem; font-weight: bold; color: #ff6b6b;">{{ number_format($comic->chapters->sum('views_count')) }}</div>
                                <div style="color: #b0b0b0; font-size: 0.8rem;">Chapter Views</div>
                            </div>
                            <div style="background: #2a2a2a; padding: 1rem; border-radius: 5px; text-align: center;">
                                <div style="font-size: 1.5rem; font-weight: bold; color: #ff6b6b;">{{ number_format($comic->unique_views_count ?? 0) }}</div>
                                <div style="color: #b0b0b0; font-size: 0.8rem;">Unique Views</div>
                            </div>
                            <div style="background: #2a2a2a; padding: 1rem; border-radius: 5px; text-align: center;">
                                <div style="font-size: 1.5rem; font-weight: bold; color: #ff6b6b;">{{ $comic->created_at->format('M d, Y') }}</div>
                                <div style="color: #b0b0b0; font-size: 0.8rem;">Upload Date</div>
                            </div>
                            @if($comic->latest_update)
                                <div style="background: #2a2a2a; padding: 1rem; border-radius: 5px; text-align: center;">
                                    <div style="font-size: 1.5rem; font-weight: bold; color: #ff6b6b;">{{ $comic->latest_update->format('M d, Y') }}</div>
                                    <div style="color: #b0b0b0; font-size: 0.8rem;">Last Updated</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Chapters Table -->
                    <div style="padding: 1.5rem;">
                        <h4 style="color: #ff6b6b; margin-bottom: 1rem;">📖 Chapter Analytics</h4>
                        @if($comic->chapters->count() > 0)
                            <div style="overflow-x: auto;">
                                <table style="width: 100%; border-collapse: collapse;">
                                    <thead>
                                        <tr style="background: #2a2a2a;">
                                            <th style="padding: 1rem; text-align: left; color: #ff6b6b; border-bottom: 2px solid #3a3a3a;">Chapter</th>
                                            <th style="padding: 1rem; text-align: center; color: #ff6b6b; border-bottom: 2px solid #3a3a3a;">Views</th>
                                            <th style="padding: 1rem; text-align: center; color: #ff6b6b; border-bottom: 2px solid #3a3a3a;">Unique Views</th>
                                            <th style="padding: 1rem; text-align: left; color: #ff6b6b; border-bottom: 2px solid #3a3a3a;">Upload Date</th>
                                            <th style="padding: 1rem; text-align: center; color: #ff6b6b; border-bottom: 2px solid #3a3a3a;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($comic->chapters->sortByDesc('number') as $chapter)
                                            <tr style="border-bottom: 1px solid #3a3a3a;" onmouseover="this.style.backgroundColor='#2a2a2a'" onmouseout="this.style.backgroundColor='transparent'">
                                                <td style="padding: 1rem;">
                                                    <span style="background: #ff6b6b; color: white; padding: 0.25rem 0.5rem; border-radius: 3px; font-size: 0.8rem; font-weight: bold;">
                                                        {{ $chapter->number }}
                                                    </span>
                                                </td>
                                                <td style="padding: 1rem; text-align: center; color: #e0e0e0;">{{ number_format($chapter->views_count) }}</td>
                                                <td style="padding: 1rem; text-align: center; color: #e0e0e0;">{{ number_format($chapter->unique_views_count ?? 0) }}</td>
                                                <td style="padding: 1rem; color: #b0b0b0; font-size: 0.9rem;">{{ $chapter->created_at->format('M d, Y') }}</td>
                                                <td style="padding: 1rem; text-align: center;">
                                                    <div style="display: flex; gap: 0.5rem; justify-content: center;">
                                                        <a href="{{ route('chapters.show', [$comic, $chapter]) }}" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">View</a>
                                                        <a href="{{ route('chapters.edit', [$comic, $chapter]) }}" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">Edit</a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p style="color: #b0b0b0; text-align: center; padding: 2rem;">No chapters found for this comic</p>
                        @endif
                    </div>

                    <!-- Action Buttons -->
                    <div style="padding: 1rem 1.5rem; background: #2a2a2a; display: flex; gap: 1rem; justify-content: flex-end;">
                        <a href="{{ route('comics.show', $comic) }}" class="btn btn-secondary" style="padding: 0.5rem 1rem;">👁️ View Comic</a>
                        <a href="{{ route('comics.edit', $comic) }}" class="btn btn-secondary" style="padding: 0.5rem 1rem;">✏️ Edit Comic</a>
                        <a href="{{ route('chapters.create', $comic) }}" class="btn" style="padding: 0.5rem 1rem;">➕ Add Chapter</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="card">
                <div class="card-body">
                    <p style="color: #b0b0b0; text-align: center; padding: 2rem;">No comics found matching the current filter.</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($comicAnalytics->hasPages())
        <div style="margin-top: 2rem; display: flex; justify-content: center;">
            {{ $comicAnalytics->links() }}
        </div>
    @endif
</div>

<script>
function toggleComicDetails(comicId) {
    const details = document.getElementById(`comic-details-${comicId}`);
    const chevron = document.getElementById(`chevron-${comicId}`);
    
    if (details.style.display === 'none') {
        details.style.display = 'block';
        chevron.style.transform = 'rotate(180deg)';
    } else {
        details.style.display = 'none';
        chevron.style.transform = 'rotate(0deg)';
    }
}

// Auto-expand first comic for better UX
document.addEventListener('DOMContentLoaded', function() {
    const firstComic = document.querySelector('.comic-analytics-card');
    if (firstComic) {
        const firstComicId = firstComic.querySelector('.comic-header').getAttribute('onclick').match(/toggleComicDetails\((\d+)\)/)[1];
        toggleComicDetails(firstComicId);
    }
});
</script>

<style>
.filter-btn:hover {
    background-color: #ff5252 !important;
    transform: translateY(-1px);
}

.filter-btn.active {
    box-shadow: 0 4px 12px rgba(255, 107, 107, 0.3);
}

.comic-header:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.comic-analytics-card {
    animation: fadeInUp 0.3s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

table th {
    position: sticky;
    top: 0;
    background: #2a2a2a;
    z-index: 10;
}

@media (max-width: 768px) {
    .analytics-summary {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .comic-header {
        padding: 1rem !important;
    }
    
    .comic-header div:first-child {
        gap: 0.5rem !important;
    }
    
    .comic-header h3 {
        font-size: 1rem !important;
    }
    
    .comic-header div[style*="display: flex; gap: 2rem"] {
        gap: 1rem !important;
        font-size: 0.8rem !important;
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
