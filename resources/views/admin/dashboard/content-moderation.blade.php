@extends('layouts.app')

@section('title', 'Content Moderation')

@section('content')
<div class="admin-content-moderation">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>🛡️ Content Moderation</h1>
        <div style="display: flex; gap: 1rem; align-items: center;">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">← Back to Dashboard</a>
            <a href="{{ route('admin.system-health') }}" class="btn btn-secondary">🔧 System Health</a>
        </div>
    </div>

    <!-- Moderation Stats -->
    <div class="moderation-stats" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
        <div class="card" style="background: linear-gradient(135deg, #2a2a2a 0%, #3a3a3a 100%);">
            <div class="card-body" style="text-align: center;">
                <div style="font-size: 2rem; margin-bottom: 0.5rem;">📚</div>
                <p style="font-size: 1.5rem; font-weight: bold; color: #ff6b6b; margin: 0;">{{ $recentComics->count() }}</p>
                <p style="color: #b0b0b0; margin: 0;">Recent Comic Updates</p>
            </div>
        </div>
        <div class="card" style="background: linear-gradient(135deg, #2a2a2a 0%, #3a3a3a 100%);">
            <div class="card-body" style="text-align: center;">
                <div style="font-size: 2rem; margin-bottom: 0.5rem;">📖</div>
                <p style="font-size: 1.5rem; font-weight: bold; color: #ff6b6b; margin: 0;">{{ $recentChapters->count() }}</p>
                <p style="color: #b0b0b0; margin: 0;">Recent Chapter Updates</p>
            </div>
        </div>
        <div class="card" style="background: linear-gradient(135deg, #2a2a2a 0%, #3a3a3a 100%);">
            <div class="card-body" style="text-align: center;">
                <div style="font-size: 2rem; margin-bottom: 0.5rem;">🚩</div>
                <p style="font-size: 1.5rem; font-weight: bold; color: #ff6b6b; margin: 0;">{{ $flaggedComments->count() }}</p>
                <p style="color: #b0b0b0; margin: 0;">Flagged Comments</p>
            </div>
        </div>
        <div class="card" style="background: linear-gradient(135deg, #2a2a2a 0%, #3a3a3a 100%);">
            <div class="card-body" style="text-align: center;">
                <div style="font-size: 2rem; margin-bottom: 0.5rem;">⚠️</div>
                <p style="font-size: 1.5rem; font-weight: bold; color: #ff6b6b; margin: 0;">{{ $reportedComics->count() }}</p>
                <p style="color: #b0b0b0; margin: 0;">Reports</p>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions" style="margin-bottom: 2rem;">
        <h2>⚡ Quick Actions</h2>
        <div class="actions" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <button onclick="reviewFlaggedContent()" class="btn btn-secondary" style="text-align: center; padding: 1rem;">
                🚩 Review Flagged Content
            </button>
            <button onclick="viewReports()" class="btn btn-secondary" style="text-align: center; padding: 1rem;">
                ⚠️ View Reports
            </button>
            <button onclick="moderationQueue()" class="btn btn-secondary" style="text-align: center; padding: 1rem;">
                📋 Moderation Queue
            </button>
            <button onclick="moderationSettings()" class="btn btn-secondary" style="text-align: center; padding: 1rem;">
                ⚙️ Moderation Settings
            </button>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 2rem;">
        <!-- Recently Updated Comics -->
        <div class="card">
            <div class="card-body">
                <h3 style="color: #ff6b6b; margin-bottom: 1rem;">📚 Recently Updated Comics</h3>
                @forelse($recentComics as $comic)
                    <div class="moderation-item" style="padding: 1rem 0; border-bottom: 1px solid #3a3a3a;">
                        <div style="display: flex; justify-content: space-between; align-items: start;">
                            <div style="flex: 1;">
                                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                                    <a href="{{ route('comics.show', $comic) }}" style="color: #e0e0e0; text-decoration: none; font-weight: bold;">
                                        {{ Str::limit($comic->title, 30) }}
                                    </a>
                                    <span class="badge badge-{{ $comic->status }}" style="font-size: 0.7rem;">
                                        {{ ucfirst($comic->status) }}
                                    </span>
                                </div>
                                <p style="color: #b0b0b0; font-size: 0.8rem; margin-bottom: 0.5rem;">
                                    by {{ $comic->user?->name ?? 'Unknown' }}
                                </p>
                                <div style="display: flex; gap: 1rem; font-size: 0.8rem; color: #999;">
                                    <span>Created: {{ $comic->created_at->format('M d, Y') }}</span>
                                    @if($comic->updated_at != $comic->created_at)
                                        <span>Updated: {{ $comic->updated_at->diffForHumans() }}</span>
                                    @endif
                                </div>
                            </div>
                            <div style="display: flex; gap: 0.5rem;">
                                <button onclick="reviewComic({{ $comic->id }})" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">
                                    Review
                                </button>
                                <a href="{{ route('comics.edit', $comic) }}" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">
                                    Edit
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <p style="color: #b0b0b0; text-align: center;">No recent comic updates</p>
                @endforelse
            </div>
        </div>

        <!-- Recently Updated Chapters -->
        <div class="card">
            <div class="card-body">
                <h3 style="color: #ff6b6b; margin-bottom: 1rem;">📖 Recently Updated Chapters</h3>
                @forelse($recentChapters as $chapter)
                    <div class="moderation-item" style="padding: 1rem 0; border-bottom: 1px solid #3a3a3a;">
                        <div style="display: flex; justify-content: space-between; align-items: start;">
                            <div style="flex: 1;">
                                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                                    <a href="{{ route('chapters.show', [$chapter->comic, $chapter]) }}" style="color: #e0e0e0; text-decoration: none; font-weight: bold;">
                                        {{ Str::limit($chapter->comic->title, 25) }} Ch.{{ $chapter->number }}
                                    </a>
                                </div>
                                <p style="color: #b0b0b0; font-size: 0.8rem; margin-bottom: 0.5rem;">
                                    {{ Str::limit($chapter->name, 30) }}
                                </p>
                                <div style="display: flex; gap: 1rem; font-size: 0.8rem; color: #999;">
                                    <span>by {{ $chapter->user?->name ?? 'Unknown' }}</span>
                                    @if($chapter->updated_at != $chapter->created_at)
                                        <span>Updated: {{ $chapter->updated_at->diffForHumans() }}</span>
                                    @endif
                                </div>
                            </div>
                            <div style="display: flex; gap: 0.5rem;">
                                <button onclick="reviewChapter({{ $chapter->id }})" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">
                                    Review
                                </button>
                                <a href="{{ route('chapters.edit', [$chapter->comic, $chapter]) }}" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">
                                    Edit
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <p style="color: #b0b0b0; text-align: center;">No recent chapter updates</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Flagged Content Section (Placeholder) -->
    @if($flaggedComments->count() > 0 || $reportedComics->count() > 0)
        <div style="margin-top: 2rem;">
            <h2 style="color: #ff6b6b; margin-bottom: 1rem;">🚩 Content Requiring Attention</h2>
            
            @if($flaggedComments->count() > 0)
                <div class="card" style="margin-bottom: 1rem;">
                    <div class="card-body">
                        <h3 style="color: #ff6b6b; margin-bottom: 1rem;">Flagged Comments</h3>
                        @foreach($flaggedComments as $comment)
                            <div class="flagged-item" style="padding: 1rem; background: #3a2a2a; border-radius: 5px; margin-bottom: 0.5rem;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <div>
                                        <p style="color: #e0e0e0; margin-bottom: 0.5rem;">{{ $comment->content }}</p>
                                        <p style="color: #b0b0b0; font-size: 0.8rem;">by {{ $comment->user->name }} • {{ $comment->created_at->diffForHumans() }}</p>
                                    </div>
                                    <div style="display: flex; gap: 0.5rem;">
                                        <button class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">Approve</button>
                                        <button class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">Remove</button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($reportedComics->count() > 0)
                <div class="card">
                    <div class="card-body">
                        <h3 style="color: #ff6b6b; margin-bottom: 1rem;">Reported Comics</h3>
                        @foreach($reportedComics as $report)
                            <div class="flagged-item" style="padding: 1rem; background: #3a2a2a; border-radius: 5px; margin-bottom: 0.5rem;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <div>
                                        <p style="color: #e0e0e0; margin-bottom: 0.5rem;">{{ $report->comic->title }} - {{ $report->reason }}</p>
                                        <p style="color: #b0b0b0; font-size: 0.8rem;">Reported by {{ $report->user->name }} • {{ $report->created_at->diffForHumans() }}</p>
                                    </div>
                                    <div style="display: flex; gap: 0.5rem;">
                                        <button class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">Review</button>
                                        <button class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">Ignore</button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @else
        <div class="card" style="margin-top: 2rem;">
            <div class="card-body" style="text-align: center; padding: 2rem;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">✅</div>
                <h3 style="color: #4caf50; margin-bottom: 0.5rem;">All Clear!</h3>
                <p style="color: #b0b0b0;">No content currently requires moderation attention.</p>
            </div>
        </div>
    @endif

    <!-- Activity Log -->
    <div class="card" style="margin-top: 2rem;">
        <div class="card-body">
            <h3 style="color: #ff6b6b; margin-bottom: 1rem;">📋 Recent Moderation Activity</h3>
            <div style="background: #2a2a2a; padding: 1rem; border-radius: 5px;">
                <p style="color: #b0b0b0; text-align: center;">
                    Activity log would be displayed here. This would include actions like:
                    content approvals, removals, user suspensions, etc.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
function reviewFlaggedContent() {
    alert('Flagged content review interface would be shown here');
}

function viewReports() {
    alert('Reports interface would be shown here');
}

function moderationQueue() {
    alert('Moderation queue would be shown here');
}

function moderationSettings() {
    alert('Moderation settings panel would be shown here');
}

function reviewComic(comicId) {
    if(confirm('Review this comic for content compliance?')) {
        alert('Comic review process would be initiated for comic ID: ' + comicId);
    }
}

function reviewChapter(chapterId) {
    if(confirm('Review this chapter for content compliance?')) {
        alert('Chapter review process would be initiated for chapter ID: ' + chapterId);
    }
}
</script>

<style>
.moderation-stats .card {
    border-left: 4px solid #ff6b6b;
}

.moderation-item:last-child {
    border-bottom: none;
}

.moderation-item:hover {
    background: rgba(255, 107, 107, 0.05);
    border-radius: 5px;
    padding-left: 0.5rem;
    padding-right: 0.5rem;
    transition: all 0.3s;
}

.flagged-item {
    border-left: 4px solid #ff9800;
}

@media (max-width: 768px) {
    .moderation-stats {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .actions {
        grid-template-columns: 1fr;
    }
    
    .admin-content-moderation > div[style*="display: grid"] {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection
