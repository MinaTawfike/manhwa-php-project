@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="admin-user-management">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>👥 User Management</h1>
        <div style="display: flex; gap: 1rem; align-items: center;">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">← Back to Dashboard</a>
            <a href="{{ route('admin.analytics') }}" class="btn btn-secondary">📊 Analytics</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- User Statistics -->
    <div class="user-stats" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
        <div class="card" style="background: linear-gradient(135deg, #2a2a2a 0%, #3a3a3a 100%);">
            <div class="card-body" style="text-align: center;">
                <div style="font-size: 2rem; margin-bottom: 0.5rem;">👥</div>
                <p style="font-size: 1.5rem; font-weight: bold; color: #ff6b6b; margin: 0;">{{ $users->total() }}</p>
                <p style="color: #b0b0b0; margin: 0;">Total Users</p>
            </div>
        </div>
        <div class="card" style="background: linear-gradient(135deg, #2a2a2a 0%, #3a3a3a 100%);">
            <div class="card-body" style="text-align: center;">
                <div style="font-size: 2rem; margin-bottom: 0.5rem;">👑</div>
                <p style="font-size: 1.5rem; font-weight: bold; color: #ff6b6b; margin: 0;">{{ $users->getCollection()->where('role', 'super_admin')->count() }}</p>
                <p style="color: #b0b0b0; margin: 0;">Super Admins</p>
            </div>
        </div>
        <div class="card" style="background: linear-gradient(135deg, #2a2a2a 0%, #3a3a3a 100%);">
            <div class="card-body" style="text-align: center;">
                <div style="font-size: 2rem; margin-bottom: 0.5rem;">🔧</div>
                <p style="font-size: 1.5rem; font-weight: bold; color: #ff6b6b; margin: 0;">{{ $users->getCollection()->where('role', 'admin')->count() }}</p>
                <p style="color: #b0b0b0; margin: 0;">Admins</p>
            </div>
        </div>
        <div class="card" style="background: linear-gradient(135deg, #2a2a2a 0%, #3a3a3a 100%);">
            <div class="card-body" style="text-align: center;">
                <div style="font-size: 2rem; margin-bottom: 0.5rem;">👤</div>
                <p style="font-size: 1.5rem; font-weight: bold; color: #ff6b6b; margin: 0;">{{ $users->getCollection()->where('role', null)->count() }}</p>
                <p style="color: #b0b0b0; margin: 0;">Regular Users</p>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions" style="margin-bottom: 2rem;">
        <h2>⚡ Quick Actions</h2>
        <div class="actions" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <button onclick="exportUsers()" class="btn btn-secondary" style="text-align: center; padding: 1rem;">
                📄 Export Users
            </button>
            <button onclick="showUserStats()" class="btn btn-secondary" style="text-align: center; padding: 1rem;">
                📊 User Statistics
            </button>
            <button onclick="bulkActions()" class="btn btn-secondary" style="text-align: center; padding: 1rem;">
                🔧 Bulk Actions
            </button>
            <button onclick="userSettings()" class="btn btn-secondary" style="text-align: center; padding: 1rem;">
                ⚙️ User Settings
            </button>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card">
        <div class="card-body">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="color: #ff6b6b;">All Users</h3>
                <div style="display: flex; gap: 1rem; align-items: center;">
                    <input type="text" placeholder="Search users..." id="userSearch" style="padding: 0.5rem; background-color: #3a3a3a; color: #e0e0e0; border: 1px solid #4a4a4a; border-radius: 5px;">
                    <select id="roleFilter" style="padding: 0.5rem; background-color: #3a3a3a; color: #e0e0e0; border: 1px solid #4a4a4a; border-radius: 5px;">
                        <option value="">All Roles</option>
                        <option value="super_admin">Super Admin</option>
                        <option value="admin">Admin</option>
                        <option value="editor">Editor</option>
                        <option value="creator">Creator</option>
                        <option value="viewer">Viewer</option>
                        <option value="null">Regular User</option>
                    </select>
                </div>
            </div>

            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #2a2a2a; border-bottom: 2px solid #3a3a3a;">
                            <th style="padding: 1rem; text-align: left; color: #ff6b6b;">User</th>
                            <th style="padding: 1rem; text-align: left; color: #ff6b6b;">Email</th>
                            <th style="padding: 1rem; text-align: left; color: #ff6b6b;">Role</th>
                            <th style="padding: 1rem; text-align: center; color: #ff6b6b;">Bookmarks</th>
                            <th style="padding: 1rem; text-align: left; color: #ff6b6b;">Joined</th>
                            <th style="padding: 1rem; text-align: center; color: #ff6b6b;">Status</th>
                            <th style="padding: 1rem; text-align: center; color: #ff6b6b;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr class="user-row" style="border-bottom: 1px solid #3a3a3a;" onmouseover="this.style.backgroundColor='#2a2a2a'" onmouseout="this.style.backgroundColor='transparent'">
                                <td style="padding: 1rem;">
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #ff6b6b, #ff5252); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div style="font-weight: bold; color: #e0e0e0;">{{ $user->name }}</div>
                                            @if($user->isSuperAdmin())
                                                <span style="color: #ff6b6b; font-size: 0.8rem;">👑 Super Admin</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 1rem; color: #b0b0b0;">{{ $user->email }}</td>
                                <td style="padding: 1rem;">
                                    <form action="{{ route('admin.users.update', $user) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <select name="role" onchange="this.form.submit()" style="padding: 0.5rem; background-color: #3a3a3a; color: #e0e0e0; border: 1px solid #4a4a4a; border-radius: 5px;">
                                            <option value="">Regular User</option>
                                            @php $roles = ['super_admin','admin','editor','creator','viewer']; @endphp
                                            @foreach($roles as $r)
                                                <option value="{{ $r }}" @if($user->role === $r) selected @endif>{{ ucfirst(str_replace('_',' ', $r)) }}</option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>
                                <td style="padding: 1rem; text-align: center;">
                                    <span style="background: #4a4a4a; color: white; padding: 0.25rem 0.5rem; border-radius: 3px; font-size: 0.8rem;">
                                        {{ $user->bookmarkedComics()->count() }}
                                    </span>
                                </td>
                                <td style="padding: 1rem; color: #b0b0b0; font-size: 0.9rem;">{{ $user->created_at->format('M d, Y') }}</td>
                                <td style="padding: 1rem; text-align: center;">
                                    @if($user->email_verified_at)
                                        <span style="color: #4caf50; font-size: 0.8rem;">✅ Verified</span>
                                    @else
                                        <span style="color: #ff9800; font-size: 0.8rem;">⏳ Pending</span>
                                    @endif
                                </td>
                                <td style="padding: 1rem; text-align: center;">
                                    <div style="display: flex; gap: 0.5rem; justify-content: center;">
                                        <button onclick="viewUserDetails({{ $user->id }})" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">View</button>
                                        <button onclick="editUser({{ $user->id }})" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">Edit</button>
                                        @if(!$user->isSuperAdmin())
                                            <button onclick="suspendUser({{ $user->id }})" class="btn btn-secondary" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">Suspend</button>
                                        @endif
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
    @if($users->hasPages())
        <div style="margin-top: 2rem; display: flex; justify-content: center;">
            {{ $users->links() }}
        </div>
    @endif
</div>

<script>
function exportUsers() {
    alert('Export functionality would be implemented here');
}

function showUserStats() {
    alert('Detailed user statistics would be shown here');
}

function bulkActions() {
    alert('Bulk actions interface would be shown here');
}

function userSettings() {
    alert('User settings panel would be shown here');
}

function viewUserDetails(userId) {
    alert('User details modal would be shown for user ID: ' + userId);
}

function editUser(userId) {
    alert('User edit form would be shown for user ID: ' + userId);
}

function suspendUser(userId) {
    if(confirm('Are you sure you want to suspend this user?')) {
        alert('User suspension would be processed for user ID: ' + userId);
    }
}

// Search functionality
document.getElementById('userSearch')?.addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('.user-row');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});

// Role filter functionality
document.getElementById('roleFilter')?.addEventListener('change', function(e) {
    const filterValue = e.target.value;
    const rows = document.querySelectorAll('.user-row');
    
    rows.forEach(row => {
        if (filterValue === '') {
            row.style.display = '';
        } else {
            // This is a simplified approach - in production, you'd want to add data attributes
            const text = row.textContent.toLowerCase();
            const matches = filterValue === 'null' ? 
                !text.includes('admin') && !text.includes('editor') && !text.includes('creator') && !text.includes('viewer') :
                text.includes(filterValue.replace('_', ' '));
            row.style.display = matches ? '' : 'none';
        }
    });
});
</script>

<style>
.user-stats .card {
    border-left: 4px solid #ff6b6b;
}

.user-row td {
    vertical-align: middle;
}

select:hover {
    border-color: #ff6b6b;
}

@media (max-width: 768px) {
    .user-stats {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .actions {
        grid-template-columns: 1fr;
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
