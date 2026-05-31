@extends('layouts.app')

@section('title', 'View Trends')

@section('content')
<div class="admin-trends">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
        <h1>📈 View Trends</h1>
        <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
            <a href="{{ route('admin.analytics') }}" class="btn btn-secondary">← Back to Analytics</a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Dashboard</a>
        </div>
    </div>

    <div style="display: flex; gap: 1rem; align-items: flex-end; flex-wrap: wrap; margin-bottom: 1rem;">
        <div>
            <label for="trendRangeSelect" style="display: block; margin-bottom: 0.5rem; color: #e0e0e0;">Select Range</label>
            <select id="trendRangeSelect" style="padding: 0.75rem 1rem; background: #1f1f1f; color: #e0e0e0; border: 1px solid #3a3a3a; border-radius: 0.5rem; min-width: 180px;">
                <option value="7">Last 7 days</option>
                <option value="28">Last 28 days</option>
                <option value="90">Last 90 days</option>
                <option value="365">Last 365 days</option>
                <option value="custom">Custom range</option>
            </select>
        </div>

        <div id="customDateRange" style="display: none; gap: 1rem; align-items: flex-end;">
            <div>
                <label for="trendStartDate" style="display: block; margin-bottom: 0.5rem; color: #e0e0e0;">From</label>
                <input id="trendStartDate" type="date" style="padding: 0.75rem 1rem; background: #1f1f1f; color: #e0e0e0; border: 1px solid #3a3a3a; border-radius: 0.5rem;" />
            </div>
            <div>
                <label for="trendEndDate" style="display: block; margin-bottom: 0.5rem; color: #e0e0e0;">To</label>
                <input id="trendEndDate" type="date" style="padding: 0.75rem 1rem; background: #1f1f1f; color: #e0e0e0; border: 1px solid #3a3a3a; border-radius: 0.5rem;" />
            </div>
            <button id="trendApplyCustom" class="btn btn-primary" style="margin-top: 0.5rem;">Apply</button>
        </div>

        <div id="trendRangeNote" style="color: #b0b0b0; margin-left: auto; min-width: 220px;"></div>
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
(function () {
    const routeUrl = '{{ route('api.admin.trends.data') }}';
    const fallback = document.getElementById('trendFallback');
    const tableBody = document.getElementById('trendTableBody');
    const rangeSelect = document.getElementById('trendRangeSelect');
    const customDateRange = document.getElementById('customDateRange');
    const startDateInput = document.getElementById('trendStartDate');
    const endDateInput = document.getElementById('trendEndDate');
    const applyButton = document.getElementById('trendApplyCustom');
    const rangeNote = document.getElementById('trendRangeNote');
    const chartCanvas = document.getElementById('viewTrendsChart');
    let trendChart = null;

    const today = new Date();
    const defaultStart = new Date(today);
    defaultStart.setDate(defaultStart.getDate() - 6);

    startDateInput.value = defaultStart.toISOString().slice(0, 10);
    endDateInput.value = today.toISOString().slice(0, 10);

    function formatRequestParams(params) {
        return Object.entries(params)
            .filter(([_, value]) => value !== undefined && value !== null && value !== '')
            .reduce((searchParams, [key, value]) => {
                searchParams.append(key, value);
                return searchParams;
            }, new URLSearchParams());
    }

    function setRangeVisibility() {
        customDateRange.style.display = rangeSelect.value === 'custom' ? 'flex' : 'none';
    }

    function setRangeNote() {
        if (rangeSelect.value === 'custom') {
            rangeNote.textContent = `Showing custom range ${startDateInput.value} → ${endDateInput.value}`;
        } else {
            rangeNote.textContent = `Showing the last ${rangeSelect.value} days.`;
        }
    }

    function renderChart(data) {
        if (trendChart) {
            trendChart.destroy();
        }

        trendChart = new Chart(chartCanvas, {
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
    }

    function renderTable(data) {
        tableBody.innerHTML = data.labels.map((label, index) => `
            <tr style="border-bottom: 1px solid #3a3a3a;">
                <td style="padding: 1rem; color: #e0e0e0;">${label}</td>
                <td style="padding: 1rem; text-align: center; color: #e0e0e0;">${data.datasets.total_views[index].toLocaleString()}</td>
                <td style="padding: 1rem; text-align: center; color: #e0e0e0;">${data.datasets.unique_visitors[index].toLocaleString()}</td>
                <td style="padding: 1rem; text-align: center; color: #e0e0e0;">${data.datasets.bookmarks_added[index].toLocaleString()}</td>
            </tr>
        `).join('');
    }

    async function loadTrends(params) {
        fallback.style.display = 'none';
        tableBody.innerHTML = '<tr><td colspan="4" style="padding: 1rem; color: #b0b0b0; text-align: center;">Loading trend data…</td></tr>';

        try {
            const query = formatRequestParams(params).toString();
            const url = query ? `${routeUrl}?${query}` : routeUrl;
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                throw new Error('Trend request failed');
            }

            const data = await response.json();

            renderChart(data);
            renderTable(data);
            setRangeNote();
        } catch (error) {
            fallback.style.display = 'block';
            tableBody.innerHTML = '<tr><td colspan="4" style="padding: 1rem; color: #b0b0b0; text-align: center;">Trend data unavailable.</td></tr>';
        }
    }

    rangeSelect.addEventListener('change', function () {
        setRangeVisibility();
        setRangeNote();

        if (this.value !== 'custom') {
            loadTrends({ range: this.value });
        }
    });

    applyButton.addEventListener('click', function (event) {
        event.preventDefault();

        if (!startDateInput.value || !endDateInput.value || startDateInput.value > endDateInput.value) {
            fallback.style.display = 'block';
            tableBody.innerHTML = '<tr><td colspan="4" style="padding: 1rem; color: #b0b0b0; text-align: center;">Please select a valid date range.</td></tr>';
            return;
        }

        loadTrends({ start: startDateInput.value, end: endDateInput.value });
    });

    setRangeVisibility();
    setRangeNote();
    loadTrends({ range: '7' });
})();
</script>
@endsection
