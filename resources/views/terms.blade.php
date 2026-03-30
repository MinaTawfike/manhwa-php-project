@extends('layouts.app')

@section('title', 'Terms & Conditions')

@section('content')
    <div style="max-width: 800px; margin: 0 auto; padding: 2rem 0;">
        <h1 style="color: #ff6b6b; margin-bottom: 2rem;">Terms & Conditions</h1>
        
        <div style="background: rgba(42, 42, 42, 0.8); padding: 2rem; border-radius: 8px; margin-bottom: 2rem;">
            <h2 style="color: #e0e0e0; margin-bottom: 1rem;">1. User Responsibilities</h2>
            <p style="margin-bottom: 1rem;">By using this service, you agree to:</p>
            <ul style="margin-left: 2rem; margin-bottom: 1.5rem;">
                <li>Provide accurate information when creating an account</li>
                <li>Respect other users and their content</li>
                <li>Not upload harmful, illegal, or inappropriate content</li>
                <li>Use the service in accordance with applicable laws</li>
            </ul>
        </div>

        <div style="background: rgba(42, 42, 42, 0.8); padding: 2rem; border-radius: 8px; margin-bottom: 2rem;">
            <h2 style="color: #e0e0e0; margin-bottom: 1rem;">2. Content Ownership</h2>
            <p style="margin-bottom: 1rem;">Regarding content you upload:</p>
            <ul style="margin-left: 2rem; margin-bottom: 1.5rem;">
                <li>You retain ownership of comics and chapters you upload</li>
                <li>You grant us license to display and distribute your content on the platform</li>
                <li>You are responsible for ensuring you have rights to uploaded content</li>
                <li>We may remove content that violates these terms</li>
            </ul>
        </div>

        <div style="background: rgba(42, 42, 42, 0.8); padding: 2rem; border-radius: 8px; margin-bottom: 2rem;">
            <h2 style="color: #e0e0e0; margin-bottom: 1rem;">3. Content Removal</h2>
            <p style="margin-bottom: 1rem;">We reserve the right to remove content that:</p>
            <ul style="margin-left: 2rem; margin-bottom: 1.5rem;">
                <li>Violates intellectual property rights</li>
                <li>Contains illegal or harmful material</li>
                <li>Violates community guidelines</li>
                <li>Is reported and verified as inappropriate</li>
            </ul>
        </div>

        <div style="background: rgba(42, 42, 42, 0.8); padding: 2rem; border-radius: 8px; margin-bottom: 2rem;">
            <h2 style="color: #e0e0e0; margin-bottom: 1rem;">4. Account Suspension</h2>
            <p style="margin-bottom: 1rem;">Your account may be suspended or terminated for:</p>
            <ul style="margin-left: 2rem; margin-bottom: 1.5rem;">
                <li>Repeated violation of these terms</li>
                <li>Malicious activity or attempts to harm the service</li>
                <li>Creation of multiple accounts without permission</li>
                <li>Any activity that compromises platform security</li>
            </ul>
        </div>

        <div style="background: rgba(42, 42, 42, 0.8); padding: 2rem; border-radius: 8px; margin-bottom: 2rem;">
            <h2 style="color: #e0e0e0; margin-bottom: 1rem;">5. Limitation of Liability</h2>
            <p style="margin-bottom: 1rem;">Please understand that:</p>
            <ul style="margin-left: 2rem; margin-bottom: 1.5rem;">
                <li>The service is provided "as is" without warranties</li>
                <li>We are not liable for content uploaded by users</li>
                <li>We are not responsible for service interruptions</li>
                <li>Your use of the service is at your own risk</li>
            </ul>
        </div>

        <div style="background: rgba(42, 42, 42, 0.8); padding: 2rem; border-radius: 8px;">
            <h2 style="color: #e0e0e0; margin-bottom: 1rem;">6. Changes to Terms</h2>
            <p style="margin-bottom: 1rem;">We reserve the right to modify these terms at any time. Continued use of the service constitutes acceptance of any changes.</p>
            <p style="color: #999; font-size: 0.9rem;">Last updated: January 2026</p>
        </div>
    </div>
@endsection
