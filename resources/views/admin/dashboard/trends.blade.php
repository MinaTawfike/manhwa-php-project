@extends('layouts.app')

@section('title', 'View Trends')

@section('content')
<div class="admin-trends">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>📈 7-Day View Trends</h1>
        <div style="display: flex; gap: 1rem; align-items: center;">
            <a href="{{ route('admin.analytics') }}" class="btn btn-secondary">← Back to Analytics</a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Dashboard</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <canvas id="viewTrendsChart" height="120"></canvas>
            <div id="trendFallback" style="display: none; color: #b0b0b0; margin-top: 1rem;">
                Trend data could not be loaded. Please refresh or check system health.
            </div>
        </div>
    </div>

    <div class="card" style="margin-top: 2rem;">
        <div class="card-body">
            <h3 style="color: #ff6b6b; margin-bottom: 1rem;">Daily Summary</h3>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #2a2a2a;">
                            <th style="padding: 1rem; text-align: left; color: #ff6b6b;">Date</th>
                            <th style="padding: 1rem; text-align: center; color: #ff6b6b;">Total Views</th>
                            <th style="padding: 1rem; text-align: center; color: #ff6b6b;">Unique Visitors</th>
                            <th style="padding: 1rem; text-align: center; color: #ff6b6b;">Bookmarks Added</th>
                        </tr>
                    </thead>
                    <tbody id="trendTableBody">
                        <tr>
                            <td colspan="4" style="padding: 1rem; color: #b0b0b0; text-align: center;">Loading trend data…</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', async function () {
    const fallback = document.getElementById('trendFallback');
    const tableBody = document.getElementById('trendTableBody');

    try {
        const response = await fetch('{{ route('api.admin.trends.data') }}', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        if (!response.ok) {
            throw new Error('Trend request failed');
        }

        const data = await response.json();

        new Chart(document.getElementById('viewTrendsChart'), {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [
                    {
                        label: 'Total Views',
                        data: data.datasets.total_views,
                        backgroundColor: 'rgba(255, 107, 107, 0.7)',
                        borderColor: '#ff6b6b',
                        borderWidth: 1,
                    },
                    {
                        label: 'Unique Visitors',
                        data: data.datasets.unique_visitors,
                        backgroundColor: 'rgba(78, 205, 196, 0.7)',
                        borderColor: '#4ecdc4',
                        borderWidth: 1,
                    },
                    {
                        label: 'Bookmarks Added',
                        data: data.datasets.bookmarks_added,
                        backgroundColor: 'rgba(255, 206, 86, 0.7)',
                        borderColor: '#ffce56',
                        borderWidth: 1,
                    },
                ],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        labels: {
                            color: '#e0e0e0',
                        },
                    },
                },
                scales: {
                    x: {
                        ticks: {
                            color: '#e0e0e0',
                        },
                        grid: {
                            color: '#3a3a3a',
                        },
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#e0e0e0',
                            precision: 0,
                        },
                        grid: {
                            color: '#3a3a3a',
                        },
                    },
                },
            },
        });

        tableBody.innerHTML = data.labels.map((label, index) => `
            <tr style="border-bottom: 1px solid #3a3a3a;">
                <td style="padding: 1rem; color: #e0e0e0;">${label}</td>
                <td style="padding: 1rem; text-align: center; color: #e0e0e0;">${data.datasets.total_views[index].toLocaleString()}</td>
                <td style="padding: 1rem; text-align: center; color: #e0e0e0;">${data.datasets.unique_visitors[index].toLocaleString()}</td>
                <td style="padding: 1rem; text-align: center; color: #e0e0e0;">${data.datasets.bookmarks_added[index].toLocaleString()}</td>
            </tr>
        `).join('');
    } catch (error) {
        fallback.style.display = 'block';
        tableBody.innerHTML = '<tr><td colspan="4" style="padding: 1rem; color: #b0b0b0; text-align: center;">Trend data unavailable.</td></tr>';
    }
});
</script>
@endsection
