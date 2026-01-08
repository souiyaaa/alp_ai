<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Guitar Tutor AI</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@latest/dist/tf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@teachablemachine/image@latest/dist/teachablemachine-image.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#2563eb',
                        secondary: '#22c55e',
                        accent: '#6366f1',
                        soft: '#f8fafc'
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gradient-to-br from-slate-50 to-slate-200 min-h-screen font-sans text-gray-800">

<!-- NAVBAR -->
<nav class="bg-white/80 backdrop-blur-md shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
        <h1 class="text-2xl font-extrabold tracking-tight text-gray-800">
            🎸 Guitar Tutor <span class="text-primary">AI</span>
        </h1>

        <div class="flex items-center gap-4 text-sm">
            <span class="text-gray-600">Hi, <b>{{ Auth::user()->name }}</b></span>
            <a href="/profile" class="px-4 py-2 rounded-lg bg-primary text-white hover:bg-blue-700 transition">
                Profile
            </a>
            <form method="POST" action="/logout">
                @csrf
                <button class="px-4 py-2 rounded-lg bg-gray-700 text-white hover:bg-gray-800 transition">
                    Logout
                </button>
            </form>
        </div>
    </div>
</nav>

<!-- HERO -->
<section class="max-w-7xl mx-auto px-6 py-10">
    <div class="bg-gradient-to-r from-primary to-accent text-white rounded-3xl p-10 shadow-xl">
        <h2 class="text-4xl font-extrabold mb-3">Live Guitar Chord Detection</h2>
        <p class="text-lg text-blue-100 max-w-2xl">
            Practice guitar smarter with real-time AI chord recognition directly from your webcam.
        </p>
    </div>
</section>

<!-- MAIN CONTENT -->
<section class="max-w-7xl mx-auto px-6 pb-16 grid lg:grid-cols-3 gap-8">

    <!-- LEFT PANEL -->
    <div class="lg:col-span-2 space-y-6">

        <!-- CONTROLS -->
        <div class="bg-white rounded-2xl shadow-md p-6 flex items-center gap-4">
            <button id="start-btn"
                class="flex-1 bg-secondary text-white py-4 rounded-xl text-lg font-semibold hover:bg-green-600 transition">
                ▶ Start Session
            </button>
            <button id="end-btn"
                class="flex-1 bg-red-500 text-white py-4 rounded-xl text-lg font-semibold hover:bg-red-600 transition disabled:opacity-50"
                disabled>
                ■ End Session
            </button>
        </div>

        <!-- TIMER -->
        <div id="timer" class="hidden bg-white rounded-2xl shadow-md p-4 text-center text-lg font-semibold">
            ⏱ Session Time:
            <span id="timer-display" class="text-primary font-bold">00:00</span>
        </div>

        <!-- WEBCAM -->
        <div class="bg-white rounded-2xl shadow-md p-6 flex justify-center">
            <video id="webcam"
                class="rounded-xl border border-gray-300 shadow-sm"
                width="360" height="260" autoplay muted playsinline>
            </video>
            <canvas id="canvas" class="hidden"></canvas>
        </div>
    </div>

    <!-- RIGHT PANEL -->
    <div class="space-y-6">

        <!-- RESULT -->
        <div class="bg-white rounded-2xl shadow-md p-6 text-center">
            <h3 class="text-lg font-semibold text-gray-600 mb-2">Detected Chord</h3>
            <div id="chord-result"
                class="text-6xl font-extrabold text-primary tracking-wide">
                Ready
            </div>
        </div>

        <!-- STATS -->
        <div id="session-stats" class="hidden grid grid-cols-2 gap-4">
            <div class="bg-blue-50 rounded-2xl p-4 text-center">
                <p class="text-sm text-gray-600">Chords Detected</p>
                <p id="stat-detections" class="text-3xl font-bold text-primary">0</p>
            </div>

            <div class="bg-green-50 rounded-2xl p-4 text-center">
                <p class="text-sm text-gray-600">Avg Confidence</p>
                <p id="stat-confidence" class="text-3xl font-bold text-green-600">0%</p>
            </div>
        </div>

    </div>

</section>

<!-- CHORD HAND GUIDE -->
<section class="max-w-7xl mx-auto px-6 pb-20">
    <div class="bg-white rounded-3xl shadow-xl p-8">
        <h3 class="text-3xl font-extrabold text-gray-800 mb-2">
            🎼 Chord Hand Position Guide
        </h3>
        <p class="text-gray-500 mb-8">
            Use this visual guide to match your hand position with the correct chord shape.
        </p>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

            <!-- C Major -->
            <div class="rounded-2xl border bg-slate-50 p-4 text-center">
                <img src="{{ asset('images/chords/c_chord.jpg') }}"
                     alt="C Major Chord Hand Position"
                     class="rounded-xl mx-auto mb-4 shadow-sm">
                <h4 class="text-xl font-bold text-primary">C Major</h4>
                <p class="text-sm text-gray-600 mt-1">
                    Index: 1st fret B<br>
                    Middle: 2nd fret D<br>
                    Ring: 3rd fret A
                </p>
            </div>

            <!-- D Minor -->
            <div class="rounded-2xl border bg-slate-50 p-4 text-center">
                <img src="{{ asset('images/chords/d_minor.jpg') }}"
                     alt="D Minor Chord Hand Position"
                     class="rounded-xl mx-auto mb-4 shadow-sm">
                <h4 class="text-xl font-bold text-primary">D Minor</h4>
                <p class="text-sm text-gray-600 mt-1">
                    Index: 1st fret E<br>
                    Middle: 2nd fret G<br>
                    Ring: 3rd fret B
                </p>
            </div>

            <!-- E Minor -->
            <div class="rounded-2xl border bg-slate-50 p-4 text-center">
                <img src="{{ asset('images/chords/e_minor.jpg') }}"
                     alt="E Minor Chord Hand Position"
                     class="rounded-xl mx-auto mb-4 shadow-sm">
                <h4 class="text-xl font-bold text-primary">E Minor</h4>
                <p class="text-sm text-gray-600 mt-1">
                    Middle: 2nd fret A<br>
                    Ring: 2nd fret D
                </p>
            </div>

            <!-- F Major -->
            <div class="rounded-2xl border bg-slate-50 p-4 text-center">
                <img src="{{ asset('images/chords/f_chord.jpg') }}"
                     alt="F Major Chord Hand Position"
                     class="rounded-xl mx-auto mb-4 shadow-sm">
                <h4 class="text-xl font-bold text-primary">F Major</h4>
                <p class="text-sm text-gray-600 mt-1">
                    Index: Barre fret 1<br>
                    Middle: 2nd fret G<br>
                    Ring: 3rd fret A<br>
                    Pinky: 3rd fret D
                </p>
            </div>

            <!-- G Major -->
            <div class="rounded-2xl border bg-slate-50 p-4 text-center">
                <img src="{{ asset('images/chords/g_chord.jpg') }}"
                     alt="G Major Chord Hand Position"
                     class="rounded-xl mx-auto mb-4 shadow-sm">
                <h4 class="text-xl font-bold text-primary">G Major</h4>
                <p class="text-sm text-gray-600 mt-1">
                    Middle: 3rd fret E<br>
                    Index: 2nd fret A<br>
                    Ring: 3rd fret B
                </p>
            </div>

            <!-- A Minor -->
            <div class="rounded-2xl border bg-slate-50 p-4 text-center">
                <img src="{{ asset('images/chords/a_minor.jpg') }}"
                     alt="A Minor Chord Hand Position"
                     class="rounded-xl mx-auto mb-4 shadow-sm">
                <h4 class="text-xl font-bold text-primary">A Minor</h4>
                <p class="text-sm text-gray-600 mt-1">
                    Index: 1st fret B<br>
                    Middle: 2nd fret D<br>
                    Ring: 2nd fret G
                </p>
            </div>

        </div>
    </div>
</section>


<script src="{{ asset('js/chord_classifier.js') }}"></script>
</body>
</html>
