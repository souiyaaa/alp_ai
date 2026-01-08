<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile • Guitar Tutor AI</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#2563eb',
                        accent: '#6366f1',
                        success: '#22c55e'
                    }
                }
            }
        }
    </script>
</head>

<body class="min-h-screen bg-gradient-to-br from-slate-100 to-slate-200 font-sans text-gray-800">

<!-- NAVBAR -->
<nav class="bg-white/80 backdrop-blur-md shadow sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
        <h1 class="text-2xl font-extrabold">
            🎸 Guitar Tutor <span class="text-primary">AI</span>
        </h1>

        <div class="flex gap-3">
            <a href="/tutor"
               class="px-4 py-2 rounded-lg bg-primary text-white hover:bg-blue-700 transition">
                Back to Practice
            </a>
            <form method="POST" action="/logout">
                @csrf
                <button
                    class="px-4 py-2 rounded-lg bg-gray-700 text-white hover:bg-gray-800 transition">
                    Logout
                </button>
            </form>
        </div>
    </div>
</nav>

<div class="max-w-7xl mx-auto px-6 py-10 space-y-10">

    <!-- PROFILE HEADER -->
    <div class="bg-white rounded-3xl shadow-lg p-8 flex items-center gap-6">
        <div
            class="w-24 h-24 rounded-full bg-gradient-to-br from-primary to-accent flex items-center justify-center text-white text-4xl font-extrabold shadow-md">
            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
        </div>
        <div>
            <h2 class="text-3xl font-bold">{{ Auth::user()->name }}</h2>
            <p class="text-gray-500">{{ Auth::user()->email }}</p>
        </div>
    </div>

    <!-- STATS -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

        <div class="bg-white rounded-2xl shadow-md p-6">
            <p class="text-sm text-gray-500">Total Sessions</p>
            <div class="flex justify-between items-end mt-2">
                <span class="text-3xl font-bold text-primary">{{ $stats['total_sessions'] }}</span>
                <span class="text-3xl">📊</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-md p-6">
            <p class="text-sm text-gray-500">Practice Time</p>
            <div class="flex justify-between items-end mt-2">
                <span class="text-3xl font-bold text-success">
                    {{ floor($stats['total_practice_time'] / 60) }}m
                </span>
                <span class="text-3xl">⏱️</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-md p-6">
            <p class="text-sm text-gray-500">Chords Detected</p>
            <div class="flex justify-between items-end mt-2">
                <span class="text-3xl font-bold text-purple-600">
                    {{ $stats['total_chords_detected'] }}
                </span>
                <span class="text-3xl">🎵</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-md p-6">
            <p class="text-sm text-gray-500">Avg Confidence</p>
            <div class="flex justify-between items-end mt-2">
                <span class="text-3xl font-bold text-orange-600">
                    {{ round($stats['average_confidence'], 1) }}%
                </span>
                <span class="text-3xl">🎯</span>
            </div>
        </div>

    </div>

    <!-- CHARTS -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">Practice Duration (Last 10 Sessions)</h3>
            <canvas id="durationChart"></canvas>
        </div>

        <div class="bg-white rounded-2xl shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">Chords Detected (Last 10 Sessions)</h3>
            <canvas id="detectionsChart"></canvas>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-md p-6">
        <h3 class="text-lg font-semibold mb-4">Average Confidence (Last 10 Sessions)</h3>
        <canvas id="confidenceChart"></canvas>
    </div>

    <!-- SESSION HISTORY -->
    <div class="bg-white rounded-2xl shadow-md p-6">
        <h3 class="text-lg font-semibold mb-4">Session History</h3>

        @if($sessions->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-gray-500 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3 text-left">Date</th>
                            <th class="px-6 py-3 text-left">Duration</th>
                            <th class="px-6 py-3 text-left">Chords</th>
                            <th class="px-6 py-3 text-left">Avg Confidence</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($sessions as $session)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4">
                                    {{ $session->started_at->format('M d, Y H:i') }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ floor($session->duration_seconds / 60) }}m {{ $session->duration_seconds % 60 }}s
                                </td>
                                <td class="px-6 py-4">
                                    {{ $session->total_detections }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 font-semibold">
                                        {{ round($session->average_confidence, 1) }}%
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-center text-gray-500 py-8">
                No practice sessions yet. Start your first session 🎸
            </p>
        @endif
    </div>

</div>

<!-- CHART SCRIPT (TIDAK DIUBAH) -->
<script>
    const chartData = @json($chartData);

    new Chart(document.getElementById('durationChart'), {
        type: 'bar',
        data: {
            labels: chartData.map(d => d.date),
            datasets: [{
                label: 'Minutes',
                data: chartData.map(d => d.duration),
                backgroundColor: 'rgba(59,130,246,.7)',
                borderColor: 'rgba(59,130,246,1)',
                borderWidth: 2
            }]
        },
        options: { responsive: true, maintainAspectRatio: true }
    });

    new Chart(document.getElementById('detectionsChart'), {
        type: 'bar',
        data: {
            labels: chartData.map(d => d.date),
            datasets: [{
                label: 'Chords',
                data: chartData.map(d => d.detections),
                backgroundColor: 'rgba(168,85,247,.7)',
                borderColor: 'rgba(168,85,247,1)',
                borderWidth: 2
            }]
        },
        options: { responsive: true, maintainAspectRatio: true }
    });

    new Chart(document.getElementById('confidenceChart'), {
        type: 'bar',
        data: {
            labels: chartData.map(d => d.date),
            datasets: [{
                label: 'Confidence %',
                data: chartData.map(d => d.confidence),
                backgroundColor: 'rgba(16,185,129,.7)',
                borderColor: 'rgba(16,185,129,1)',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: { y: { beginAtZero: true, max: 100 } }
        }
    });
</script>

</body>
</html>
