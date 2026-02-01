<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Manhua Website</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, rgba(26, 26, 26, 0.85) 0%, rgba(30, 30, 30, 0.85) 100%), 
                        url('/anime-night-sky-illustration.jpg');
            background-attachment: fixed;
            background-size: cover;
            background-position: center;
            color: #e0e0e0;
            line-height: 1.6;
        }

        header {
            background: linear-gradient(135deg, #1f1f1f 0%, #2a2a2a 100%);
            border-bottom: 2px solid #ff6b6b;
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            color: #ff6b6b;
            text-decoration: none;
        }

        nav {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        nav a {
            color: #e0e0e0;
            text-decoration: none;
            transition: color 0.3s;
        }

        nav a:hover {
            color: #ff6b6b;
        }

        .auth-links {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .btn {
            display: inline-block;
            padding: 0.7rem 1.5rem;
            background-color: #ff6b6b;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
            font-size: 0.95rem;
        }

        .btn:hover {
            background-color: #ff5252;
        }

        .btn-secondary {
            background-color: #4a4a4a;
        }

        .btn-secondary:hover {
            background-color: #5a5a5a;
        }

        main {
            min-height: 80vh;
            padding: 2rem 0;
        }

        footer {
            background-color: #1f1f1f;
            color: #666;
            text-align: center;
            padding: 2rem;
            border-top: 1px solid #3a3a3a;
            margin-top: 4rem;
        }

        /* Profile Specific Styles */
        .welcome-section {
            background: linear-gradient(135deg, #1f1f1f 0%, #2a2a2a 100%);
            border: 1px solid #3a3a3a;
            border-radius: 10px;
            padding: 2.5rem;
            margin-bottom: 2rem;
            text-align: center;
        }

        .welcome-section h1 {
            font-size: 2.5rem;
            color: #ff6b6b;
            margin-bottom: 0.5rem;
        }

        .welcome-section p {
            color: #b0b0b0;
            font-size: 1.05rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: linear-gradient(135deg, #1f1f1f 0%, #2a2a2a 100%);
            border: 1px solid #3a3a3a;
            border-radius: 8px;
            padding: 1.5rem;
            text-align: center;
        }

        .stat-number {
            font-size: 2rem;
            color: #ff6b6b;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #b0b0b0;
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .action-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            padding: 1.2rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }

        .action-btn-primary {
            background: linear-gradient(135deg, #ff6b6b 0%, #ff5252 100%);
        }

        .action-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 107, 107, 0.3);
        }

        .action-btn-secondary {
            background: linear-gradient(135deg, #4a4a4a 0%, #5a5a5a 100%);
        }

        .action-btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(74, 74, 74, 0.3);
        }

        .action-btn-tertiary {
            background: linear-gradient(135deg, #666666 0%, #777777 100%);
        }

        .action-btn-tertiary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 102, 102, 0.3);
        }

        .form-section {
            background: linear-gradient(135deg, #1f1f1f 0%, #2a2a2a 100%);
            border: 1px solid #3a3a3a;
            border-radius: 10px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .form-section h2 {
            color: #ff6b6b;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #e0e0e0;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 0.75rem 1rem;
            background-color: #2a2a2a;
            border: 1px solid #3a3a3a;
            color: #e0e0e0;
            border-radius: 6px;
            font-size: 0.95rem;
            transition: all 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #ff6b6b;
            background-color: #2f2f2f;
            box-shadow: 0 0 8px rgba(255, 107, 107, 0.2);
        }

        .form-error {
            color: #ff6b6b;
            font-size: 0.85rem;
            margin-top: 0.3rem;
        }

        .form-submit {
            width: 100%;
            padding: 0.85rem 1.5rem;
            background-color: #ff6b6b;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .form-submit:hover {
            background-color: #ff5252;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 107, 107, 0.3);
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 1rem;
        }

        .info-item label {
            color: #b0b0b0;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-item div {
            color: #e0e0e0;
            font-size: 1rem;
            margin-top: 0.3rem;
            font-weight: 500;
        }

        .success-message {
            background-color: rgba(76, 175, 80, 0.1);
            border: 1px solid #4caf50;
            color: #81c784;
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 2rem;
            text-align: center;
        }

        @media (max-width: 768px) {
            .info-grid {
                grid-template-columns: 1fr;
            }

            .actions-grid {
                grid-template-columns: 1fr;
            }

            .header-content {
                flex-direction: column;
                gap: 1rem;
            }

            nav {
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <a href="{{ route('comics.index') }}" class="logo">🎌 Manhua</a>
                <nav>
                    <a href="{{ route('comics.index') }}">Comics</a>
                    <a href="{{ route('bookmarks.index') }}">🔖 Bookmarks</a>
                    @if(auth()->user()->isSuperAdmin())
                        <a href="{{ route('admin.users.index') }}">Admin</a>
                    @endif
                </nav>

                <div class="auth-links">
                    <span>Welcome, {{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-secondary">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <main>
        <div class="container">
            <!-- Welcome Section -->
            <div class="welcome-section">
                <h1>Welcome back, {{ $user->name }}!</h1>
                <p>Your Manhwa profile dashboard</p>
            </div>

            <!-- Success Message -->
            @if (session('status') === 'password-updated')
                <div class="success-message">
                    ✓ Password updated successfully!
                </div>
            @endif

            <!-- Quick Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number">{{ auth()->user()->bookmarkedComics?->count() ?? 0 }}</div>
                    <div class="stat-label">Bookmarked Comics</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">{{ auth()->user()->lastChaptersPerComic?->count() ?? 0 }}</div>
                    <div class="stat-label">Comics Reading</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" style="font-size: 0.9rem;">{{ $user->email }}</div>
                    <div class="stat-label">Email</div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="actions-grid">
                <a href="{{ route('comics.index') }}" class="action-btn action-btn-primary">
                    📚 Browse Comics
                </a>
                <a href="{{ route('bookmarks.index') }}" class="action-btn action-btn-secondary">
                    🔖 My Bookmarks
                </a>
                <a href="#change-password" class="action-btn action-btn-tertiary">
                    🔐 Change Password
                </a>
                <a href="{{ route('profile.edit') }}" class="action-btn" style="background: linear-gradient(135deg, #888888 0%, #999999 100%);">
                    👤 Edit Profile
                </a>
            </div>

            <!-- Change Password Section -->
            <div class="form-section" id="change-password">
                <h2>Change Password</h2>
                
                <form method="POST" action="{{ route('profile.updatePassword') }}">
                    @csrf
                    @method('PATCH')

                    <!-- Current Password -->
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input 
                            id="current_password" 
                            type="password" 
                            name="current_password" 
                            required
                            placeholder="Enter your current password"
                        />
                        @if ($errors->updatePassword->has('current_password'))
                            <div class="form-error">{{ $errors->updatePassword->first('current_password') }}</div>
                        @endif
                    </div>

                    <!-- New Password -->
                    <div class="form-group">
                        <label for="password">New Password</label>
                        <input 
                            id="password" 
                            type="password" 
                            name="password" 
                            required
                            placeholder="Enter a new password (min. 8 characters)"
                        />
                        @if ($errors->updatePassword->has('password'))
                            <div class="form-error">{{ $errors->updatePassword->first('password') }}</div>
                        @endif
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-group">
                        <label for="password_confirmation">Confirm Password</label>
                        <input 
                            id="password_confirmation" 
                            type="password" 
                            name="password_confirmation" 
                            required
                            placeholder="Confirm your new password"
                        />
                        @if ($errors->updatePassword->has('password_confirmation'))
                            <div class="form-error">{{ $errors->updatePassword->first('password_confirmation') }}</div>
                        @endif
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="form-submit">Update Password</button>
                </form>
            </div>

            <!-- Account Info Section -->
            <div class="form-section">
                <h2>Account Information</h2>
                
                <div class="info-grid">
                    <div class="info-item">
                        <label>Name</label>
                        <div>{{ $user->name }}</div>
                    </div>
                    <div class="info-item">
                        <label>Email</label>
                        <div>{{ $user->email }}</div>
                    </div>
                    <div class="info-item">
                        <label>Role</label>
                        <div style="color: {{ $user->isSuperAdmin() ? '#4caf50' : '#ff9800' }};">{{ ucfirst($user->role) }}</div>
                    </div>
                    <div class="info-item">
                        <label>Member Since</label>
                        <div>{{ $user->created_at->format('M d, Y') }}</div>
                    </div>
                </div>

                <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #3a3a3a;">
                    <a href="{{ route('profile.edit') }}" style="display: inline-block; color: #ff6b6b; text-decoration: none; font-weight: 600; transition: color 0.3s;" onmouseover="this.style.color='#ff5252';" onmouseout="this.style.color='#ff6b6b';">
                        → Edit full profile details
                    </a>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2026 Manhwa Website. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>