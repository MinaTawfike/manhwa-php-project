@extends('layouts.app')

@section('title', 'Privacy Policy')

@section('content')
    <div style="max-width: 800px; margin: 0 auto; padding: 2rem 0;">
        <h1 style="color: #ff6b6b; margin-bottom: 2rem;">Privacy Policy</h1>
        
        <div style="background: rgba(42, 42, 42, 0.8); padding: 2rem; border-radius: 8px; margin-bottom: 2rem;">
            <h2 style="color: #e0e0e0; margin-bottom: 1rem;">1. Data We Collect</h2>
            <p style="margin-bottom: 1rem;">We collect the following information:</p>
            <ul style="margin-left: 2rem; margin-bottom: 1.5rem;">
                <li><strong>Email Address:</strong> For account creation and communication</li>
                <li><strong>Authentication Data:</strong> Login sessions and security tokens</li>
                <li><strong>Uploaded Content:</strong> Comics, chapters, and related images</li>
                <li><strong>User Activity:</strong> Bookmarks, ratings, and comments</li>
                <li><strong>Technical Data:</strong> IP address, browser type, and access logs</li>
            </ul>
        </div>

        <div style="background: rgba(42, 42, 42, 0.8); padding: 2rem; border-radius: 8px; margin-bottom: 2rem;">
            <h2 style="color: #e0e0e0; margin-bottom: 1rem;">2. How We Use Your Data</h2>
            <p style="margin-bottom: 1rem;">Your data is used to:</p>
            <ul style="margin-left: 2rem; margin-bottom: 1.5rem;">
                <li>Provide and maintain the service</li>
                <li>Authenticate users and secure accounts</li>
                <li>Store and display your uploaded content</li>
                <li>Enable features like bookmarks and ratings</li>
                <li>Send important service notifications</li>
                <li>Analyze usage to improve the platform</li>
            </ul>
        </div>

        <div style="background: rgba(42, 42, 42, 0.8); padding: 2rem; border-radius: 8px; margin-bottom: 2rem;">
            <h2 style="color: #e0e0e0; margin-bottom: 1rem;">3. Cookies & Sessions</h2>
            <p style="margin-bottom: 1rem;">We use:</p>
            <ul style="margin-left: 2rem; margin-bottom: 1.5rem;">
                <li><strong>Session Cookies:</strong> To keep you logged in during your visit</li>
                <li><strong>Security Tokens:</strong> To protect against unauthorized access</li>
                <li><strong>Preference Cookies:</strong> To remember your settings</li>
                <li>Cookies are essential for the service to function properly</li>
            </ul>
        </div>

        <div style="background: rgba(42, 42, 42, 0.8); padding: 2rem; border-radius: 8px; margin-bottom: 2rem;">
            <h2 style="color: #e0e0e0; margin-bottom: 1rem;">4. Data Protection</h2>
            <p style="margin-bottom: 1rem;">We protect your data by:</p>
            <ul style="margin-left: 2rem; margin-bottom: 1.5rem;">
                <li>Using secure HTTPS connections</li>
                <li>Encrypting passwords with strong algorithms</li>
                <li>Implementing access controls and authentication</li>
                <li>Regular security updates and monitoring</li>
                <li>Not selling personal data to third parties</li>
            </ul>
        </div>

        <div style="background: rgba(42, 42, 42, 0.8); padding: 2rem; border-radius: 8px; margin-bottom: 2rem;">
            <h2 style="color: #e0e0e0; margin-bottom: 1rem;">5. Data Retention</h2>
            <p style="margin-bottom: 1rem;">We retain data as follows:</p>
            <ul style="margin-left: 2rem; margin-bottom: 1.5rem;">
                <li><strong>Account Data:</strong> Until you delete your account</li>
                <li><strong>Uploaded Content:</strong> Until you remove it or we delete it per terms</li>
                <li><strong>Activity Logs:</strong> For 90 days for security purposes</li>
                <li><strong>Session Data:</strong> Automatically deleted when sessions expire</li>
            </ul>
        </div>

        <div style="background: rgba(42, 42, 42, 0.8); padding: 2rem; border-radius: 8px;">
            <h2 style="color: #e0e0e0; margin-bottom: 1rem;">6. Your Rights</h2>
            <p style="margin-bottom: 1rem;">You have the right to:</p>
            <ul style="margin-left: 2rem; margin-bottom: 1.5rem;">
                <li>Access your personal data</li>
                <li>Update or correct your information</li>
                <li>Delete your account and associated data</li>
                <li>Request data export</li>
                <li>Opt out of non-essential data processing</li>
            </ul>
            <p style="color: #999; font-size: 0.9rem;">Last updated: January 2026</p>
        </div>
    </div>
@endsection
