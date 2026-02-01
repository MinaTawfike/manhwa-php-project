<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background: linear-gradient(135deg, rgba(26, 26, 26, 0.95) 0%, rgba(30, 30, 30, 0.95) 100%), 
                            url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 600"><rect fill="%23000" width="1200" height="600"/></svg>');
                background-attachment: fixed;
                background-size: cover;
                background-position: center;
                color: #e0e0e0;
                line-height: 1.6;
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
            }

            .auth-container {
                width: 100%;
                max-width: 450px;
                padding: 2rem;
            }

            .auth-header {
                text-align: center;
                margin-bottom: 2.5rem;
            }

            .auth-logo {
                font-size: 2.5rem;
                font-weight: bold;
                color: #ff6b6b;
                text-decoration: none;
                display: inline-block;
                margin-bottom: 1rem;
            }

            .auth-title {
                font-size: 1.8rem;
                color: #fff;
                margin-bottom: 0.5rem;
            }

            .auth-subtitle {
                color: #b0b0b0;
                font-size: 0.95rem;
            }

            .auth-card {
                background: linear-gradient(135deg, #1f1f1f 0%, #2a2a2a 100%);
                border: 1px solid #3a3a3a;
                border-radius: 10px;
                padding: 2rem;
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5);
            }

            .form-group {
                margin-bottom: 1.5rem;
            }

            label {
                display: block;
                margin-bottom: 0.5rem;
                color: #e0e0e0;
                font-weight: 500;
                font-size: 0.95rem;
            }

            input[type="text"],
            input[type="email"],
            input[type="password"] {
                width: 100%;
                padding: 0.75rem 1rem;
                background-color: #2a2a2a;
                border: 1px solid #3a3a3a;
                color: #e0e0e0;
                border-radius: 6px;
                font-size: 0.95rem;
                transition: all 0.3s;
            }

            input[type="text"]:focus,
            input[type="email"]:focus,
            input[type="password"]:focus {
                outline: none;
                border-color: #ff6b6b;
                background-color: #2f2f2f;
                box-shadow: 0 0 8px rgba(255, 107, 107, 0.2);
            }

            input[type="checkbox"] {
                accent-color: #ff6b6b;
            }

            .form-error {
                color: #ff6b6b;
                font-size: 0.85rem;
                margin-top: 0.3rem;
            }

            .btn-primary {
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
                text-decoration: none;
                display: inline-block;
                text-align: center;
            }

            .btn-primary:hover {
                background-color: #ff5252;
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(255, 107, 107, 0.3);
            }

            .form-links {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-top: 1rem;
                font-size: 0.9rem;
            }

            .form-links a {
                color: #ff6b6b;
                text-decoration: none;
                transition: color 0.3s;
            }

            .form-links a:hover {
                color: #ff5252;
                text-decoration: underline;
            }

            .checkbox-group {
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }

            .checkbox-group input[type="checkbox"] {
                width: 18px;
                height: 18px;
                cursor: pointer;
            }

            .checkbox-group label {
                margin: 0;
                cursor: pointer;
                font-weight: 400;
            }

            .auth-message {
                padding: 1rem;
                margin-bottom: 1.5rem;
                border-radius: 6px;
                font-size: 0.95rem;
            }

            .auth-message.success {
                background-color: rgba(76, 175, 80, 0.1);
                border: 1px solid #4caf50;
                color: #81c784;
            }

            .auth-message.error {
                background-color: rgba(244, 67, 54, 0.1);
                border: 1px solid #f44336;
                color: #ef5350;
            }
        </style>
    </head>
    <body>
        <div class="auth-container">
            <div class="auth-header">
                <a href="/" class="auth-logo">Manhwa</a>
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
