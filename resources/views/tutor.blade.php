<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Live Chord Classifier</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@latest/dist/tf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@teachablemachine/image@latest/dist/teachablemachine-image.min.js"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary-blue': '#3b82f6',
                        'success-green': '#10b981',
                        'light-gray': '#f4f4f9',
                    }
                }
            }
        }
    </script>
</head>
<body class="font-sans bg-light-gray min-h-screen">
    
    <!-- Navigation -->
    <nav class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">🎸 Guitar Tutor</h1>
            <div class="flex gap-4 items-center">
                <span class="text-gray-600">Welcome, {{ Auth::user()->name }}!</span>
                <a href="/profile" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    Profile
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

    <div class="flex flex-col items-center p-5">
        
        <!-- Session Controls -->
        <div class="mt-5 mb-3 flex gap-4">
            <button id="start-btn" class="bg-green-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-700 transition text-lg">
                Start Session
            </button>
            <button id="end-btn" class="bg-red-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-700 transition text-lg" disabled>
                End Session
            </button>
        </div>

        <!-- Session Timer -->
        <div id="timer" class="text-xl font-semibold text-gray-700 mb-3 hidden">
            Session Time: <span id="timer-display">00:00</span>
        </div>

        <!-- Webcam Container -->
        <div id="webcam-container" class="mt-3 p-5 rounded-lg shadow-lg bg-white">
            <video id="webcam" width="320" height="240" autoplay muted playsinline class="border border-gray-300 rounded-md"></video>
            <canvas id="canvas" class="hidden"></canvas>
        </div>

        <!-- Result Container -->
        <div id="result-container" class="mt-5 p-5 rounded-lg shadow-lg bg-white w-full max-w-sm text-center">
            <h2 class="text-xl font-semibold text-gray-700">Detected Chord:</h2>
            <div id="chord-result" class="text-5xl font-extrabold text-primary-blue mb-4">Ready</div>
            
            <!-- Session Stats -->
            <div id="session-stats" class="mt-4 grid grid-cols-2 gap-4 text-sm hidden">
                <div class="bg-blue-50 p-3 rounded-lg">
                    <div class="text-gray-600">Chords Detected</div>
                    <div id="stat-detections" class="text-2xl font-bold text-blue-600">0</div>
                </div>
                <div class="bg-green-50 p-3 rounded-lg">
                    <div class="text-gray-600">Avg Confidence</div>
                    <div id="stat-confidence" class="text-2xl font-bold text-green-600">0%</div>
                </div>
            </div>
        </div>

    </div>

    <script src="{{ asset('js/chord_classifier.js') }}"></script>
</body>
</html>