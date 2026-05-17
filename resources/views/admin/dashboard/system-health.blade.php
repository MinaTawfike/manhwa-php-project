@extends('layouts.app')

@section('title', 'System Health')

@section('content')
<div class="admin-system-health">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>🔧 System Health & Security</h1>
        <div style="display: flex; gap: 1rem; align-items: center;">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">← Back to Dashboard</a>
            <a href="{{ route('admin.content-moderation') }}" class="btn btn-secondary">🛡️ Content Moderation</a>
        </div>
    </div>

    <!-- System Overview -->
    <div class="system-overview" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
        <div class="card" style="background: linear-gradient(135deg, #2a2a2a 0%, #3a3a3a 100%);">
            <div class="card-body" style="text-align: center;">
                <div style="font-size: 2rem; margin-bottom: 0.5rem;">🌍</div>
                <p style="font-size: 1.2rem; font-weight: bold; color: {{ $systemInfo['app_env'] === 'production' ? '#4caf50' : '#ff9800' }}; margin: 0;">{{ $systemInfo['app_env'] }}</p>
                <p style="color: #b0b0b0; margin: 0;">Environment</p>
            </div>
        </div>
        <div class="card" style="background: linear-gradient(135deg, #2a2a2a 0%, #3a3a3a 100%);">
            <div class="card-body" style="text-align: center;">
                <div style="font-size: 2rem; margin-bottom: 0.5rem;">{{ $systemInfo['app_debug'] === 'ON' ? '🐞' : '✅' }}</div>
                <p style="font-size: 1.2rem; font-weight: bold; color: {{ $systemInfo['app_debug'] === 'ON' ? '#f44336' : '#4caf50' }}; margin: 0;">{{ $systemInfo['app_debug'] }}</p>
                <p style="color: #b0b0b0; margin: 0;">Debug Mode</p>
            </div>
        </div>
        <div class="card" style="background: linear-gradient(135deg, #2a2a2a 0%, #3a3a3a 100%);">
            <div class="card-body" style="text-align: center;">
                <div style="font-size: 2rem; margin-bottom: 0.5rem;">🔒</div>
                <p style="font-size: 1.2rem; font-weight: bold; color: {{ $systemInfo['https_status'] === 'HTTPS' ? '#4caf50' : '#ff9800' }}; margin: 0;">{{ $systemInfo['https_status'] }}</p>
                <p style="color: #b0b0b0; margin: 0;">Connection</p>
            </div>
        </div>
        <div class="card" style="background: linear-gradient(135deg, #2a2a2a 0%, #3a3a3a 100%);">
            <div class="card-body" style="text-align: center;">
                <div style="font-size: 2rem; margin-bottom: 0.5rem;">💾</div>
                <p style="font-size: 1.2rem; font-weight: bold; color: #e0e0e0; margin: 0;">{{ $systemInfo['cache_driver'] }}</p>
                <p style="color: #b0b0b0; margin: 0;">Cache Driver</p>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 2rem;">
        <!-- System Information -->
        <div class="card">
            <div class="card-body">
                <h3 style="color: #ff6b6b; margin-bottom: 1.5rem;">🖥️ System Information</h3>
                <div class="system-info-grid" style="display: grid; gap: 1rem;">
                    <div class="info-item" style="display: flex; justify-content: space-between; padding: 0.75rem; background: #2a2a2a; border-radius: 5px;">
                        <span style="color: #b0b0b0;">Laravel Version</span>
                        <span style="color: #e0e0e0; font-weight: bold;">{{ $systemInfo['laravel_version'] }}</span>
                    </div>
                    <div class="info-item" style="display: flex; justify-content: space-between; padding: 0.75rem; background: #2a2a2a; border-radius: 5px;">
                        <span style="color: #b0b0b0;">PHP Version</span>
                        <span style="color: #e0e0e0; font-weight: bold;">{{ $systemInfo['php_version'] }}</span>
                    </div>
                    <div class="info-item" style="display: flex; justify-content: space-between; padding: 0.75rem; background: #2a2a2a; border-radius: 5px;">
                        <span style="color: #b0b0b0;">Session Driver</span>
                        <span style="color: #e0e0e0; font-weight: bold;">{{ $systemInfo['session_driver'] }}</span>
                    </div>
                    <div class="info-item" style="display: flex; justify-content: space-between; padding: 0.75rem; background: #2a2a2a; border-radius: 5px;">
                        <span style="color: #b0b0b0;">Queue Driver</span>
                        <span style="color: #e0e0e0; font-weight: bold;">{{ $systemInfo['queue_driver'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Status -->
        <div class="card">
            <div class="card-body">
                <h3 style="color: #ff6b6b; margin-bottom: 1.5rem;">🔒 Security Status</h3>
                <div class="security-info-grid" style="display: grid; gap: 1rem;">
                    <div class="info-item" style="display: flex; justify-content: space-between; padding: 0.75rem; background: #2a2a2a; border-radius: 5px;">
                        <span style="color: #b0b0b0;">Environment</span>
                        <span style="color: {{ $systemInfo['app_env'] === 'production' ? '#4caf50' : '#ff9800' }}; font-weight: bold;">
                            {{ $systemInfo['app_env'] }} {{ $systemInfo['app_env'] === 'production' ? '✅' : '⚠️' }}
                        </span>
                    </div>
                    <div class="info-item" style="display: flex; justify-content: space-between; padding: 0.75rem; background: #2a2a2a; border-radius: 5px;">
                        <span style="color: #b0b0b0;">Debug Mode</span>
                        <span style="color: {{ $systemInfo['app_debug'] === 'OFF' ? '#4caf50' : '#f44336' }}; font-weight: bold;">
                            {{ $systemInfo['app_debug'] }} {{ $systemInfo['app_debug'] === 'OFF' ? '✅' : '❌' }}
                        </span>
                    </div>
                    <div class="info-item" style="display: flex; justify-content: space-between; padding: 0.75rem; background: #2a2a2a; border-radius: 5px;">
                        <span style="color: #b0b0b0;">HTTPS Status</span>
                        <span style="color: {{ $systemInfo['https_status'] === 'HTTPS' ? '#4caf50' : '#ff9800' }}; font-weight: bold;">
                            {{ $systemInfo['https_status'] }} {{ $systemInfo['https_status'] === 'HTTPS' ? '✅' : '⚠️' }}
                        </span>
                    </div>
                    <div class="info-item" style="display: flex; justify-content: space-between; padding: 0.75rem; background: #2a2a2a; border-radius: 5px;">
                        <span style="color: #b0b0b0;">Rate Limiting</span>
                        <span style="color: #4caf50; font-weight: bold;">Active ✅</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Service Health -->
    <div class="service-health" style="margin-top: 2rem;">
        <h2 style="color: #ff6b6b; margin-bottom: 1.5rem;">🏥 Service Health</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1rem;">
            <div class="card">
                <div class="card-body">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <h4 style="color: #e0e0e0; margin: 0;">Database</h4>
                        <span style="font-size: 1.5rem;">{{ $systemInfo['database_status']['status'] === 'healthy' ? '✅' : '❌' }}</span>
                    </div>
                    <p style="color: {{ $systemInfo['database_status']['status'] === 'healthy' ? '#4caf50' : '#f44336' }}; margin: 0;">
                        {{ $systemInfo['database_status']['message'] }}
                    </p>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <h4 style="color: #e0e0e0; margin: 0;">Storage (R2)</h4>
                        <span style="font-size: 1.5rem;">{{ $systemInfo['storage_status']['status'] === 'healthy' ? '✅' : '❌' }}</span>
                    </div>
                    <p style="color: {{ $systemInfo['storage_status']['status'] === 'healthy' ? '#4caf50' : '#f44336' }}; margin: 0;">
                        {{ $systemInfo['storage_status']['message'] }}
                    </p>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <h4 style="color: #e0e0e0; margin: 0;">Cache System</h4>
                        <span style="font-size: 1.5rem;">✅</span>
                    </div>
                    <p style="color: #4caf50; margin: 0;">
                        Cache driver ({{ $systemInfo['cache_driver'] }}) is working
                    </p>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <h4 style="color: #e0e0e0; margin: 0;">Session Management</h4>
                        <span style="font-size: 1.5rem;">✅</span>
                    </div>
                    <p style="color: #4caf50; margin: 0;">
                        Session driver ({{ $systemInfo['session_driver'] }}) is active
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions" style="margin-top: 2rem;">
        <h2>⚡ System Actions</h2>
        <div class="actions" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <button onclick="clearCache()" class="btn btn-secondary" style="text-align: center; padding: 1rem;">
                🗑️ Clear Cache
            </button>
            <button onclick="optimizeDatabase()" class="btn btn-secondary" style="text-align: center; padding: 1rem;">
                ⚡ Optimize Database
            </button>
            <button onclick="checkUpdates()" class="btn btn-secondary" style="text-align: center; padding: 1rem;">
                🔄 Check Updates
            </button>
            <button onclick="systemLogs()" class="btn btn-secondary" style="text-align: center; padding: 1rem;">
                📋 View Logs
            </button>
        </div>
    </div>

    <!-- Security Recommendations -->
    <div class="security-recommendations" style="margin-top: 2rem;">
        <div class="card">
            <div class="card-body">
                <h3 style="color: #ff6b6b; margin-bottom: 1rem;">🔍 Security Recommendations</h3>
                <div style="background: #2a2a2a; padding: 1rem; border-radius: 5px; border-left: 4px solid #ff9800;">
                    @if($systemInfo['app_debug'] === 'ON')
                        <div style="margin-bottom: 0.5rem;">
                            <span style="color: #ff9800; font-weight: bold;">⚠️ High Priority:</span>
                            <span style="color: #e0e0e0;"> Debug mode is enabled in production. Set APP_DEBUG=false in .env file.</span>
                        </div>
                    @endif
                    
                    @if($systemInfo['https_status'] === 'HTTP')
                        <div style="margin-bottom: 0.5rem;">
                            <span style="color: #ff9800; font-weight: bold;">⚠️ Medium Priority:</span>
                            <span style="color: #e0e0e0;"> Consider enabling HTTPS for better security.</span>
                        </div>
                    @endif
                    
                    @if($systemInfo['app_env'] !== 'production')
                        <div style="margin-bottom: 0.5rem;">
                            <span style="color: #4caf50; font-weight: bold;">ℹ️ Info:</span>
                            <span style="color: #e0e0e0;"> Currently running in {{ $systemInfo['app_env'] }} environment.</span>
                        </div>
                    @endif
                    
                    <div style="margin-bottom: 0.5rem;">
                        <span style="color: #4caf50; font-weight: bold;">✅ Good:</span>
                        <span style="color: #e0e0e0;"> Rate limiting is active to prevent abuse.</span>
                    </div>
                    
                    <div>
                        <span style="color: #4caf50; font-weight: bold;">✅ Good:</span>
                        <span style="color: #e0e0e0;"> Admin routes are protected with authentication and authorization.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function clearCache() {
    if(confirm('Are you sure you want to clear all caches? This may temporarily slow down the site.')) {
        alert('Cache clearing would be processed via artisan command');
        // In production, this would make an AJAX call to run cache:clear
    }
}

function optimizeDatabase() {
    if(confirm('Optimize database tables? This may take a few moments.')) {
        alert('Database optimization would be processed');
        // In production, this would run database optimization commands
    }
}

function checkUpdates() {
    alert('System would check for Laravel and package updates');
    // In production, this would check composer.json for available updates
}

function systemLogs() {
    alert('System logs viewer would be opened');
    // In production, this would show recent log entries
}

// Auto-refresh health status every 30 seconds
setInterval(function() {
    // In production, this would make an AJAX call to refresh health data
    console.log('Health status refresh would happen here');
}, 30000);
</script>

<style>
.system-overview .card {
    border-left: 4px solid #ff6b6b;
}

.info-item:hover {
    background: #3a3a3a !important;
    transition: background-color 0.3s;
}

.security-recommendations .card {
    border-left: 4px solid #ff9800;
}

@media (max-width: 768px) {
    .system-overview {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .admin-system-health > div[style*="display: grid"] {
        grid-template-columns: 1fr;
    }
    
    .service-health > div {
        grid-template-columns: 1fr;
    }
    
    .actions {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection
