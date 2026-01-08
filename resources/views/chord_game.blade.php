<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audio Chord Trainer</title>
    
    <script src="https://cdn.tailwindcss.com"></script>

    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@latest/dist/tf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow-models/speech-commands@latest/dist/speech-commands.min.js"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'primary-blue': '#3b82f6',
                        'success-green': '#10b981',
                        'light-gray': '#f4f4f9',
                        'active-red': '#ef4444',
                    },
                    animation: {
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                    }
                }
            }
        }
    </script>
</head>

<body class="font-sans flex flex-col items-center p-5 bg-light-gray min-h-screen">

    <nav class="w-full max-w-lg mb-4 flex justify-between text-sm text-gray-500">
        <a href="/chord-classifier" class="hover:text-primary-blue transition">← Back to Free Play</a>
        <span class="font-semibold text-primary-blue">Audio Mode</span>
    </nav>

    <h1 class="text-3xl font-bold mb-6 text-gray-800">🎸 Chord Listening Game</h1>

    <div id="start-screen" class="flex flex-col items-center justify-center p-10 bg-white rounded-lg shadow-lg max-w-md w-full text-center">
        <div class="mb-4 text-6xl">🎙️</div>
        <h2 class="text-xl font-bold text-gray-700 mb-2">Ready to Play?</h2>
        <p class="text-gray-500 mb-6">We need access to your microphone to hear your guitar.</p>
        <button onclick="startGameContext()" class="px-8 py-3 bg-primary-blue text-white font-bold rounded-full hover:bg-blue-600 transition transform hover:scale-105 shadow-md">
            Enable Mic & Start
        </button>
    </div>

    <div id="game-ui" class="hidden w-full flex-col items-center">
        
        <div class="flex justify-between w-full max-w-lg mb-6 gap-4">
            <div class="bg-white p-4 rounded-lg shadow text-center w-1/3 border-b-4 border-primary-blue">
                <p class="text-gray-400 text-xs uppercase tracking-wide">Score</p>
                <p id="score-display" class="text-4xl font-bold text-gray-800">0</p>
            </div>

            <div class="bg-white p-6 rounded-lg shadow text-center w-2/3 relative overflow-hidden border-b-4 border-gray-300">
                <p class="text-gray-400 text-xs uppercase tracking-wide">Play This Chord</p>
                <p id="target-chord" class="text-5xl font-black text-gray-800 mt-1">...</p>
                
                <div id="success-overlay" class="absolute inset-0 bg-success-green flex items-center justify-center opacity-0 transition-opacity duration-300 pointer-events-none">
                    <span class="text-white font-bold text-3xl animate-bounce">PERFECT! 🎉</span>
                </div>
            </div>
        </div>

        <div class="mt-4 p-8 rounded-full bg-white shadow-inner border border-gray-100 relative flex items-center justify-center w-32 h-32">
            <div id="listening-indicator" class="absolute w-full h-full rounded-full bg-red-100 opacity-0 transition-opacity duration-500"></div>
            <div class="text-4xl">👂</div>
        </div>
        <p class="mt-4 text-gray-400 text-sm">Listening for your guitar...</p>
        
        <div class="mt-2 text-xs text-gray-300">
            Detected: <span id="detected-debug" class="font-mono">...</span>
        </div>

    </div>
    <div id="game-ui" class="hidden w-full flex-col items-center">
        <div id="debug-graph-container" class="mt-6 w-full max-w-md bg-white p-4 rounded-lg shadow-sm border border-gray-200">
            <h3 class="text-xs font-bold text-gray-400 uppercase mb-2">Live Audio Analysis</h3>
            <div id="prediction-bars" class="space-y-2"></div>
        </div>

    </div>

    <script src="{{ asset('js/chord_game.js') }}"></script>

    <script>
        // This function is called by the Start Button
        function startGameContext() {
            // Hide Start Screen
            document.getElementById('start-screen').classList.add('hidden');
            // Show Game UI
            document.getElementById('game-ui').classList.remove('hidden');
            document.getElementById('game-ui').classList.add('flex');
            
            // Add pulse animation to indicator
            document.getElementById('listening-indicator').classList.add('animate-ping');
            document.getElementById('listening-indicator').classList.remove('opacity-0');

            // Call the init() function inside chord_game.js
            if (typeof init === "function") {
                init();
            } else {
                console.error("init() function not found in chord_game.js");
            }
        }
    </script>

</body>
</html>