<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Manhwa Website</title>
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

        .alert {
            padding: 1rem;
            margin-bottom: 2rem;
            border-radius: 5px;
        }

        .alert-success {
            background-color: #4caf50;
            color: white;
        }

        .alert-error {
            background-color: #f44336;
            color: white;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 2rem;
        }

        .card {
            background: #2a2a2a;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
            border: 1px solid #3a3a3a;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(255, 107, 107, 0.2);
        }

        .card img {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }

        .card-body {
            padding: 1.5rem;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
            color: #ff6b6b;
        }

        .card-text {
            font-size: 0.9rem;
            margin-bottom: 1rem;
            color: #b0b0b0;
        }

        .badge {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            margin-bottom: 0.5rem;
        }

        .badge-ongoing {
            background-color: #4caf50;
            color: white;
        }

        .badge-completed {
            background-color: #2196f3;
            color: white;
        }

        .badge-hiatus {
            background-color: #ff9800;
            color: white;
        }

        .chapter-list {
            background: #2a2a2a;
            border-radius: 8px;
            padding: 1.5rem;
            margin-top: 2rem;
        }

        .chapter-item {
            padding: 1rem;
            border-bottom: 1px solid #3a3a3a;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chapter-item:last-child {
            border-bottom: none;
        }

        .chapter-info h3 {
            color: #ff6b6b;
            margin-bottom: 0.5rem;
        }

        .chapter-info p {
            font-size: 0.9rem;
            color: #b0b0b0;
        }

        .reader-container {
            background: #2a2a2a;
            border-radius: 8px;
            padding: 2rem;
            margin-top: 2rem;
        }

        .pages-container {
            display: flex;
            flex-direction: column;
            gap: 2rem;
            margin: 2rem 0;
        }

        .page {
            text-align: center;
        }

        .page img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin: 1.5rem 0;
        }

        form {
            display: inline-block;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }

        input[type="text"],
        input[type="number"],
        textarea,
        select {
            width: 100%;
            padding: 0.8rem;
            background-color: #3a3a3a;
            color: #e0e0e0;
            border: 1px solid #4a4a4a;
            border-radius: 5px;
            font-size: 1rem;
        }

        textarea {
            resize: vertical;
            min-height: 150px;
        }

        .rating-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .rating-btn {
            padding: 0.5rem 1rem;
            background-color: #4a4a4a;
            color: #e0e0e0;
            border: 2px solid #4a4a4a;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .rating-btn:hover,
        .rating-btn.active {
            background-color: #ff6b6b;
            border-color: #ff6b6b;
            color: white;
        }

        footer {
            background-color: #1f1f1f;
            color: #666;
            text-align: center;
            padding: 2rem;
            border-top: 1px solid #3a3a3a;
            margin-top: 4rem;
        }

        h1 {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #ff6b6b;
        }

        h2 {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            color: #ff6b6b;
            margin-top: 2rem;
        }

        @media (max-width: 768px) {
            .grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
                gap: 1rem;
            }

            nav {
                gap: 1rem;
            }

            .header-content {
                flex-direction: column;
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
                    @auth
                        <a href="{{ route('comics.index') }}" style="color: #e0e0e0; text-decoration: none; transition: color 0.3s;" onmouseover="this.style.color='#ff6b6b'" onmouseout="this.style.color='#e0e0e0'">Manhuas</a>
                        <a href="{{ route('bookmarks.index') }}" style="color: #e0e0e0; text-decoration: none; transition: color 0.3s;" onmouseover="this.style.color='#ff6b6b'" onmouseout="this.style.color='#e0e0e0'">🔖 Bookmarks</a>
                        <span>Welcome, {{ auth()->user()->name }}</span>
                        @if (Route::has('logout'))
                        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-secondary">Logout</button>
                        </form>
                        @endif
                    @else
                        <a href="{{ route('comics.index') }}">Comics</a>
                        <div class="auth-links">
                            @if (Route::has('login'))
                                <a href="{{ route('login') }}" class="btn btn-secondary">Login</a>
                            @endif
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn">Sign Up</a>
                            @endif
                        </div>
                    @endauth
                </nav>
            </div>
        </div>
    </header>

    <main>
        <div class="container">
            @if ($message = Session::get('success'))
                <div class="alert alert-success">
                    {{ $message }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-error">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2026 Manhwa Website. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
