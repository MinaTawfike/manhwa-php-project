@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="admin-dashboard">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>🎌 Admin Dashboard</h1>
        <div style="display: flex; gap: 1rem; align-items: center;">
            <a href="{{ route('admin.analytics') }}" class="btn btn-secondary">📊 Analytics</a>
            <a href="{{ route('admin.trends') }}" class="btn btn-secondary">📈 Trends</a>
            <a href="{{ route('admin.system-health') }}" class="btn btn-secondary">🔧 System Health</a>
        </div>
    </div>

    <!-- Overview Cards -->
    <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
        <div class="card">
            <div class="card-body">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <h3 style="color: #ff6b6b; margin-bottom: 0.5rem;">📚 Total Comics</h3>
                        <p style="font-size: 2rem; font-weight: bold; margin: 0;">{{ $stats['total_comics'] }}</p>
                    </div>
                    <div style="font-size: 3rem; opacity: 0.3;">📚</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <h3 style="color: #ff6b6b; margin-bottom: 0.5rem;">📖 Total Chapters</h3>
                        <p style="font-size: 2rem; font-weight: bold; margin: 0;">{{ $stats['total_chapters'] }}</p>
                    </div>
                    <div style="font-size: 3rem; opacity: 0.3;">📖</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <h3 style="color: #ff6b6b; margin-bottom: 0.5rem;">👥 Total Users</h3>
                        <p style="font-size: 2rem; font-weight: bold; margin: 0;">{{ $stats['total_users'] }}</p>
                    </div>
                    <div style="font-size: 3rem; opacity: 0.3;">👥</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <h3 style="color: #ff6b6b; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                            <svg class="admin-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="width:1.3rem; height:1.3rem;">
                                <path d="M12 5C7 5 2.73 8.11 1 12C2.73 15.89 7 19 12 19C17 19 21.27 15.89 23 12C21.27 8.11 17 5 12 5Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Comic Views
                        </h3>
                        <p style="font-size: 2rem; font-weight: bold; margin: 0;">{{ number_format($stats['total_comic_views']) }}</p>
                    </div>
                    <div style="opacity: 0.3;">
                        <svg class="admin-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="width:3rem; height:3rem;">
                            <path d="M12 5C7 5 2.73 8.11 1 12C2.73 15.89 7 19 12 19C17 19 21.27 15.89 23 12C21.27 8.11 17 5 12 5Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <h3 style="color: #ff6b6b; margin-bottom: 0.5rem;">📖 Chapter Views</h3>
                        <p style="font-size: 2rem; font-weight: bold; margin: 0;">{{ number_format($stats['total_chapter_views']) }}</p>
                    </div>
                    <div style="font-size: 3rem; opacity: 0.3;">📖</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <h3 style="color: #ff6b6b; margin-bottom: 0.5rem;">📊 Total Views</h3>
                        <p style="font-size: 2rem; font-weight: bold; margin: 0;">{{ number_format($stats['total_views']) }}</p>
                    </div>
                    <div style="font-size: 3rem; opacity: 0.3;">📊</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <h3 style="color: #ff6b6b; margin-bottom: 0.5rem;">👥 Unique Comic Views</h3>
                        <p style="font-size: 2rem; font-weight: bold; margin: 0;">{{ number_format($stats['unique_comic_views']) }}</p>
                    </div>
                    <div style="font-size: 3rem; opacity: 0.3;">👥</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <h3 style="color: #ff6b6b; margin-bottom: 0.5rem;">👥 Unique Chapter Views</h3>
                        <p style="font-size: 2rem; font-weight: bold; margin: 0;">{{ number_format($stats['unique_chapter_views']) }}</p>
                    </div>
                    <div style="font-size: 3rem; opacity: 0.3;">👥</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <h3 style="color: #ff6b6b; margin-bottom: 0.5rem;">👤 Today's Visitors</h3>
                        <p style="font-size: 2rem; font-weight: bold; margin: 0;">{{ number_format($stats['unique_visitors_today']) }}</p>
                    </div>
                    <div style="font-size: 3rem; opacity: 0.3;">👤</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <h3 style="color: #ff6b6b; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
                            <svg class="admin-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="width:1.3rem; height:1.3rem;">
                                <rect x="3" y="7" width="18" height="12" rx="2" stroke="currentColor" stroke-width="1.8"/>
                                <path d="M7 11H17" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                <path d="M7 15H13" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            </svg>
                            Storage Usage
                        </h3>
                        <p style="font-size: 1.5rem; font-weight: bold; margin: 0;">{{ $stats['storage_usage']['total_size'] }}</p>
                        <p style="font-size: 0.9rem; color: #b0b0b0; margin: 0;">{{ $stats['storage_usage']['file_count'] }} files</p>
                    </div>
                    <div style="opacity: 0.3;">
                        <svg class="admin-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="width:3rem; height:3rem;">
                            <rect x="3" y="6" width="18" height="12" rx="2" stroke="currentColor" stroke-width="1.8"/>
                            <path d="M3 10H21" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            <path d="M12 14C13.6569 14 15 12.6569 15 11C15 9.34315 13.6569 8 12 8C10.3431 8 9 9.34315 9 11C9 12.6569 10.3431 14 12 14Z" stroke="currentColor" stroke-width="1.8"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions" style="margin-bottom: 3rem;">
        <h2>⚡ Quick Actions</h2>
        <div class="actions" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <a href="{{ route('comics.create') }}" class="btn" style="text-align: center; padding: 1rem;">
                ➕ Add Comic
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary" style="text-align: center; padding: 1rem;">
                👥 Manage Users
            </a>
            <a href="{{ route('admin.analytics') }}" class="btn btn-secondary" style="text-align: center; padding: 1rem;">
                📊 View Analytics
            </a>
            <a href="{{ route('admin.system-health') }}" class="btn btn-secondary" style="text-align: center; padding: 1rem;">
                🔧 System Settings
            </a>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="recent-activity" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
        <!-- Recent Comics -->
        <div class="card">
            <div class="card-body">
                <h3 style="color: #ff6b6b; margin-bottom: 1rem;">📚 Recent Comics</h3>
                @forelse($recentComics as $comic)
                    <div class="activity-item" style="padding: 0.75rem 0; border-bottom: 1px solid #3a3a3a;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <a href="{{ route('comics.show', $comic) }}" style="color: #e0e0e0; text-decoration: none; font-weight: bold;">
                                    {{ Str::limit($comic->title, 30) }}
                                </a>
                                <p style="font-size: 0.8rem; color: #b0b0b0; margin: 0.25rem 0;">
                                    by {{ $comic->user?->name ?? 'Unknown' }}
                                </p>
                            </div>
                            <div style="text-align: right;">
                                <p style="font-size: 0.8rem; color: #b0b0b0; margin: 0;">
                                    {{ $comic->created_at->diffForHumans() }}
                                </p>
                                <span class="badge badge-{{ $comic->status }}" style="font-size: 0.7rem;">
                                    {{ ucfirst($comic->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <p style="color: #b0b0b0; text-align: center;">No recent comics</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Chapters -->
        <div class="card">
            <div class="card-body">
                <h3 style="color: #ff6b6b; margin-bottom: 1rem;">📖 Recent Chapters</h3>
                @forelse($recentChapters as $chapter)
                    <div class="activity-item" style="padding: 0.75rem 0; border-bottom: 1px solid #3a3a3a;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <a href="{{ route('chapters.show', [$chapter->comic, $chapter]) }}" style="color: #e0e0e0; text-decoration: none; font-weight: bold;">
                                    {{ $chapter->comic->title }} Ch.{{ $chapter->number }}
                                </a>
                                <p style="font-size: 0.8rem; color: #b0b0b0; margin: 0.25rem 0;">
                                    {{ Str::limit($chapter->name, 25) }}
                                </p>
                            </div>
                            <div style="text-align: right;">
                                <p style="font-size: 0.8rem; color: #b0b0b0; margin: 0;">
                                    {{ $chapter->created_at->diffForHumans() }}
                                </p>
                                <p style="font-size: 0.7rem; color: #999; margin: 0;">
                                    by {{ $chapter->user?->name ?? 'Unknown' }}
                                </p>
                            </div>
                        </div>
                    </div>
                @empty
                    <p style="color: #b0b0b0; text-align: center;">No recent chapters</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Users -->
        <div class="card">
            <div class="card-body">
                <h3 style="color: #ff6b6b; margin-bottom: 1rem;">👥 Recent Users</h3>
                @forelse($recentUsers as $user)
                    <div class="activity-item" style="padding: 0.75rem 0; border-bottom: 1px solid #3a3a3a;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <p style="color: #e0e0e0; font-weight: bold; margin: 0;">
                                    {{ $user->name }}
                                </p>
                                <p style="font-size: 0.8rem; color: #b0b0b0; margin: 0.25rem 0;">
                                    {{ $user->email }}
                                </p>
                            </div>
                            <div style="text-align: right;">
                                <p style="font-size: 0.8rem; color: #b0b0b0; margin: 0;">
                                    {{ $user->created_at->diffForHumans() }}
                                </p>
                                <span class="badge" style="font-size: 0.7rem; background-color: #4a4a4a;">
                                    {{ ucfirst($user->role ?? 'user') }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <p style="color: #b0b0b0; text-align: center;">No recent users</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Analytics Preview -->
    <div class="analytics-preview" style="margin-top: 3rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2>📊 Comic Analytics Preview</h2>
            <div style="display: flex; gap: 1rem;">
                <a href="{{ route('admin.trends') }}" class="btn btn-secondary">View Trends</a>
                <a href="{{ route('admin.analytics') }}" class="btn btn-secondary">View Full Analytics</a>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div style="display: flex; gap: 0.5rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
            <a href="?filter=most_viewed" class="filter-tab {{ $analyticsFilter == 'most_viewed' ? 'active' : '' }}" style="padding: 0.5rem 1rem; background-color: {{ $analyticsFilter == 'most_viewed' ? '#ff6b6b' : '#4a4a4a' }}; color: white; text-decoration: none; border-radius: 5px;">
                Most Viewed
            </a>
            <a href="?filter=recently_updated" class="filter-tab {{ $analyticsFilter == 'recently_updated' ? 'active' : '' }}" style="padding: 0.5rem 1rem; background-color: {{ $analyticsFilter == 'recently_updated' ? '#ff6b6b' : '#4a4a4a' }}; color: white; text-decoration: none; border-radius: 5px;">
                Recently Updated
            </a>
            <a href="?filter=most_bookmarked" class="filter-tab {{ $analyticsFilter == 'most_bookmarked' ? 'active' : '' }}" style="padding: 0.5rem 1rem; background-color: {{ $analyticsFilter == 'most_bookmarked' ? '#ff6b6b' : '#4a4a4a' }}; color: white; text-decoration: none; border-radius: 5px;">
                Most Bookmarked
            </a>
            <a href="?filter=newest" class="filter-tab {{ $analyticsFilter == 'newest' ? 'active' : '' }}" style="padding: 0.5rem 1rem; background-color: {{ $analyticsFilter == 'newest' ? '#ff6b6b' : '#4a4a4a' }}; color: white; text-decoration: none; border-radius: 5px;">
                Newest
            </a>
        </div>

        <div class="analytics-grid" style="display: grid; gap: 1rem;">
            @forelse($comicAnalytics as $comic)
                <div class="card analytics-card">
                    <div class="card-body">
                        <div style="display: flex; justify-content: space-between; align-items: start;">
                            <div style="flex: 1;">
                                <h4 style="color: #ff6b6b; margin-bottom: 0.5rem;">{{ $comic->title }}</h4>
                                <div style="display: flex; gap: 2rem; margin-top: 1rem; flex-wrap: wrap;">
                                    <div>
                                        <p style="font-size: 0.8rem; color: #b0b0b0; margin: 0;">Chapters</p>
                                        <p style="font-weight: bold; margin: 0;">{{ $comic->chapters->count() }}</p>
                                    </div>
                                    <div>
                                        <p style="font-size: 0.8rem; color: #b0b0b0; margin: 0;">Bookmarks</p>
                                        <p style="font-weight: bold; margin: 0;">{{ $comic->bookmarkedBy->count() }}</p>
                                    </div>
                                    <div>
                                        <p style="font-size: 0.8rem; color: #b0b0b0; margin: 0;">Status</p>
                                        <span class="badge badge-{{ $comic->status }}" style="font-size: 0.7rem;">
                                            {{ ucfirst($comic->status) }}
                                        </span>
                                    </div>
                                    <div>
                                        <p style="font-size: 0.8rem; color: #b0b0b0; margin: 0;">Created</p>
                                        <p style="font-size: 0.9rem; margin: 0;">{{ $comic->created_at->format('M d, Y') }}</p>
                                    </div>
                                    @if($comic->latest_update)
                                        <div>
                                            <p style="font-size: 0.8rem; color: #b0b0b0; margin: 0;">Last Updated</p>
                                            <p style="font-size: 0.9rem; margin: 0;">{{ $comic->latest_update->format('M d, Y') }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div style="display: flex; gap: 0.5rem;">
                                <a href="{{ route('comics.show', $comic) }}" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.8rem;">
                                    View
                                </a>
                                <a href="{{ route('comics.edit', $comic) }}" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.8rem;">
                                    Edit
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card">
                    <div class="card-body">
                        <p style="color: #b0b0b0; text-align: center;">No comics found</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>

<style>
.stats-grid .card {
    border-left: 4px solid #ff6b6b;
}

.activity-item:last-child {
    border-bottom: none;
}

.analytics-card {
    border-left: 4px solid #4a4a4a;
    transition: border-color 0.3s;
}

.analytics-card:hover {
    border-left-color: #ff6b6b;
}

.filter-tab:hover {
    background-color: #ff5252 !important;
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .recent-activity {
        grid-template-columns: 1fr;
    }
    
    .actions {
        grid-template-columns: 1fr;
    }
    
    .analytics-grid {
        gap: 0.5rem;
    }
}
</style>
@endsection
