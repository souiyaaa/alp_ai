// public/js/chord_game.js
const base_url = window.location.origin;
// --- 1. Configuration & Paths ---
const modelPath = base_url + "/chords_audio/";
const modelURL = modelPath + "model.json";
const metadataURL = modelPath + "metadata.json";

// --- 2. DOM Elements (UI) ---
const targetDisplay = document.getElementById("target-chord");
const scoreDisplay = document.getElementById("score-display");
const successOverlay = document.getElementById("success-overlay");
const debugDisplay = document.getElementById("detected-debug");

// --- 3. Game State Variables ---
let recognizer;
let currentTarget = "";
let score = 0;
let availableChords = []; // Will be populated automatically from the model
let isRoundActive = false;

// --- 4. Initialization Function ---
async function init() {
    try {
        // A. Create the recognizer
        recognizer = speechCommands.create(
            "BROWSER_FFT", // Fourier transform type
            undefined,     // vocabulary (undefined uses the one in the model)
            modelURL,
            metadataURL
        );

        // B. Ensure model is loaded
        await recognizer.ensureModelLoaded();
        
        const labels = recognizer.wordLabels(); // Get chord names (C, Dm, etc.)
        console.log("Audio Labels:", labels);

        // C. Start Listening
        recognizer.listen(result => {
            // result.scores contains the probability for each label
            const scores = result.scores;
            
            let highestScore = 0;
            let detectedChord = "";

            // Loop through scores to find the dominant sound
            for (let i = 0; i < labels.length; i++) {
                if (scores[i] > highestScore) {
                    highestScore = scores[i];
                    detectedChord = labels[i];
                }
            }

            // D. Update Debug Text (Visual Feedback)
            if (debugDisplay) {
                // Only show if confidence is decent, otherwise show "..."
                if (highestScore > 0.30) {
                    debugDisplay.textContent = `${detectedChord} (${Math.round(highestScore * 100)}%)`;
                } else {
                    debugDisplay.textContent = "...";
                }
            }

            // E. Core Game Logic
            // Check if:
            // 1. The round is active (game is running)
            // 2. The detected chord matches the target
            // 3. Confidence is high enough (> 85% recommended for audio stability)
            if (isRoundActive && detectedChord === currentTarget && highestScore > 0.85) {
                triggerSuccess();
            }

        }, {
            includeSpectrogram: false,
            probabilityThreshold: 0.75,
            invokeCallbackOnNoiseAndUnknown: true,
            overlapFactor: 0.5 // How often it checks (0.5 = 50% overlap)
        });
        
        // F. Start the Game Loop
        startGame();

    } catch (error) {
        console.error("Error initializing audio model:", error);
        if (targetDisplay) targetDisplay.textContent = "Error Loading";
    }
}

// --- 5. Game Helper Functions ---

function startGame() {
    score = 0;
    if (scoreDisplay) scoreDisplay.textContent = score;

    // Get chord labels from the model
    // Filter out technical labels like "Background Noise" or "Unknown" to get only music chords
    if (recognizer) {
        availableChords = recognizer.wordLabels().filter(l => 
            l !== "_background_noise_" && l !== "_unknown_"
        );
    }

    nextRound();
}

function nextRound() {
    // Pick a random chord
    const randomIndex = Math.floor(Math.random() * availableChords.length);
    currentTarget = availableChords[randomIndex];

    // Update UI
    if (targetDisplay) targetDisplay.textContent = currentTarget;
    if (successOverlay) successOverlay.classList.add("opacity-0"); // Hide success banner

    // Enable scoring
    isRoundActive = true;
}

function triggerSuccess() {
    // Disable scoring immediately so it doesn't trigger multiple times for one strum
    isRoundActive = false;
    
    // Update Score
    score++;
    if (scoreDisplay) scoreDisplay.textContent = score;
    
    // Show Success Animation
    if (successOverlay) successOverlay.classList.remove("opacity-0");
    
    // Wait 1.5 seconds before starting the next round
    setTimeout(nextRound, 1500);
}