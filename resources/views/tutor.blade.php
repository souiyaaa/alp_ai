<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

<body class="font-sans flex flex-col items-center p-5 bg-light-gray min-h-screen">

    <h1 class="text-3xl font-bold mt-4 mb-8 text-gray-800">🎸 Live Chord Classifier for Learning</h1>

    <div id="webcam-container" class="mt-5 p-5 rounded-lg shadow-lg bg-white">
        <video id="webcam" width="320" height="240" autoplay muted playsinline class="border border-gray-300 rounded-md"></video>
        <canvas id="canvas" class="hidden"></canvas>
    </div>

    <div id="result-container" class="mt-5 p-5 rounded-lg shadow-lg bg-white w-full max-w-sm text-center">
        <h2 class="text-xl font-semibold text-gray-700">Detected Chord:</h2>
        
        <div id="chord-result" class="text-5xl font-extrabold text-primary-blue mb-4">Loading...</div>
        
        <div id="label-container" class="hidden">
            </div> 
    </div>

    <script src="{{ asset('js/chord_classifier.js') }}"></script>

</body>
</html>