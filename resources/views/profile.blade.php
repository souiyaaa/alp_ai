<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Guitar Tutor</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    
    <!-- Navigation -->
    <nav class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">🎸 Guitar Tutor</h1>
            <div class="flex gap-4">
                <a href="/tutor" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    Back to Practice
                </a>
                <form method="POST" action="/logout" class="inline">
                    @csrf
                    <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-8">
        
        <!-- Profile Header -->
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
            <div class="flex items-center gap-6">
                <div class="w-24 h-24 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-4xl font-bold">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">{{ Auth::user()->name }}</h2>
                    <p class="text-gray-600">{{ Auth::user()->email }}</p>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Total Sessions</p>
                        <p class="text-3xl font-bold text-blue-600">{{ $stats['total_sessions'] }}</p>
                    </div>
                    <div class="text-4xl">📊</div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Practice Time</p>
                        <p class="text-3xl font-bold text-green-600">{{ floor($stats['total_practice_time'] / 60) }}m</p>
                    </div>
                    <div class="text-4xl">⏱️</div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Chords Detected</p>
                        <p class="text-3xl font-bold text-purple-600">{{ $stats['total_chords_detected'] }}</p>
                    </div>
                    <div class="text-4xl">🎵</div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Avg Confidence</p>
                        <p class="text-3xl font-bold text-orange-600">{{ round($stats['average_confidence'], 1) }}%</p>
                    </div>
                    <div class="text-4xl">🎯</div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Practice Duration (Last 10 Sessions)</h3>
                <canvas id="durationChart"></canvas>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Chords Detected (Last 10 Sessions)</h3>
                <canvas id="detectionsChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Average Confidence (Last 10 Sessions)</h3>
            <canvas id="confidenceChart"></canvas>
        </div>

        <!-- Session History -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Session History</h3>
            
            @if($sessions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chords</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Confidence</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($sessions as $session)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $session->started_at->format('M d, Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ floor($session->duration_seconds / 60) }}m {{ $session->duration_seconds % 60 }}s
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $session->total_detections }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full">
                                            {{ round($session->average_confidence, 1) }}%
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-600 text-center py-8">No practice sessions yet. Start your first session!</p>
            @endif
        </div>

    </div>

    <script>
        const chartData = @json($chartData);

        // Duration Chart
        new Chart(document.getElementById('durationChart'), {
            type: 'bar',
            data: {
                labels: chartData.map(d => d.date),
                datasets: [{
                    label: 'Minutes',
                    data: chartData.map(d => d.duration),
                    backgroundColor: 'rgba(59, 130, 246, 0.7)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Minutes'
                        }
                    }
                }
            }
        });

        // Detections Chart
        new Chart(document.getElementById('detectionsChart'), {
            type: 'bar',
            data: {
                labels: chartData.map(d => d.date),
                datasets: [{
                    label: 'Chords',
                    data: chartData.map(d => d.detections),
                    backgroundColor: 'rgba(168, 85, 247, 0.7)',
                    borderColor: 'rgba(168, 85, 247, 1)',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Chords'
                        }
                    }
                }
            }
        });

        // Confidence Chart
        new Chart(document.getElementById('confidenceChart'), {
            type: 'bar',
            data: {
                labels: chartData.map(d => d.date),
                datasets: [{
                    label: 'Confidence %',
                    data: chartData.map(d => d.confidence),
                    backgroundColor: 'rgba(16, 185, 129, 0.7)',
                    borderColor: 'rgba(16, 185, 129, 1)',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        title: {
                            display: true,
                            text: 'Confidence %'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>