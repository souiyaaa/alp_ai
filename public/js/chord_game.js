// public/js/chord_game.js

// --- 1. Configuration & Paths ---
const base_url = window.location.origin;
const modelPath = base_url + "/chords_audio/"; 
const modelURL = modelPath + "model.json";
const metadataURL = modelPath + "metadata.json";

// --- 2. DOM Elements (UI) ---
const targetDisplay = document.getElementById("target-chord");
const scoreDisplay = document.getElementById("score-display");
const successOverlay = document.getElementById("success-overlay");
const debugDisplay = document.getElementById("detected-debug");
const graphContainer = document.getElementById("prediction-bars");

// --- 3. Game State Variables ---
let recognizer;
let currentTarget = "";
let score = 0;
let availableChords = []; 
let isRoundActive = false;
let allLabels = []; 

// --- 4. Initialization Function ---
async function init() {
    try {
        // A. Create the recognizer
        recognizer = speechCommands.create(
            "BROWSER_FFT", 
            undefined,     
            modelURL,
            metadataURL
        );

        // B. Ensure model is loaded
        await recognizer.ensureModelLoaded();
        
        allLabels = recognizer.wordLabels(); 
        
        // Setup the Visual Graph UI
        setupGraphUI(allLabels);

        // C. Start Listening
        recognizer.listen(result => {
            const scores = result.scores;
            
            let highestScore = 0;
            let detectedChord = "";

            // --- DETECTION LOOP ---
            for (let i = 0; i < allLabels.length; i++) {
                const label = allLabels[i];
                const probability = scores[i];
                
                // 1. Still update the graph for everything (so you can SEE the noise)
                updateGraphBar(i, probability);

                // --- CRITICAL FIX START ---
                // If this label is noise, SKIP it. Do not let it become the "detectedChord".
                if (label === "_background_noise_" || label === "_unknown_") {
                    continue; 
                }
                // --- CRITICAL FIX END ---

                // 2. Find the winner among the REAL chords only
                if (probability > highestScore) {
                    highestScore = probability;
                    detectedChord = label;
                }
            }

            // D. Update Debug Text
            if (debugDisplay) {
                // We set a threshold (0.30) so low-confidence guesses (like silence) show as "..."
                if (highestScore > 0.30) {
                    debugDisplay.textContent = `${detectedChord} (${Math.round(highestScore * 100)}%)`;
                } else {
                    debugDisplay.textContent = "...";
                }
            }

            // E. Game Logic
            if (isRoundActive && detectedChord === currentTarget && highestScore > 0.85) {
                triggerSuccess();
            }

        }, {
            includeSpectrogram: false,
            probabilityThreshold: 0.75,
            invokeCallbackOnNoiseAndUnknown: true,
            overlapFactor: 0.5 
        });
        
        startGame();

    } catch (error) {
        console.error("Error initializing audio model:", error);
        if (targetDisplay) targetDisplay.textContent = "Error Loading";
    }
}

// ... (Rest of the file: setupGraphUI, updateGraphBar, startGame, nextRound, triggerSuccess remain the same) ...

// --- 5. Game Helper Functions ---

function setupGraphUI(labels) {
    if (!graphContainer) return;
    graphContainer.innerHTML = ''; 

    labels.forEach((label, index) => {
        const row = document.createElement('div');
        row.className = "flex items-center text-sm mb-1"; // Added mb-1 for spacing
        
        const nameSpan = document.createElement('div');
        nameSpan.className = "w-32 font-mono text-gray-600 truncate text-xs"; // Made label smaller
        nameSpan.textContent = label;

        const barContainer = document.createElement('div');
        barContainer.className = "flex-1 h-3 bg-gray-100 rounded-full overflow-hidden ml-2";
        
        const bar = document.createElement('div');
        bar.id = `graph-bar-${index}`;
        bar.className = "h-full bg-primary-blue transition-all duration-75"; 
        bar.style.width = "0%";

        barContainer.appendChild(bar);
        row.appendChild(nameSpan);
        row.appendChild(barContainer);
        graphContainer.appendChild(row);
    });
}

function updateGraphBar(index, probability) {
    const bar = document.getElementById(`graph-bar-${index}`);
    if (bar) {
        const percent = Math.round(probability * 100);
        bar.style.width = `${percent}%`;
        
        if (probability > 0.85) {
            bar.classList.remove("bg-primary-blue");
            bar.classList.add("bg-success-green");
        } else {
            bar.classList.add("bg-primary-blue");
            bar.classList.remove("bg-success-green");
        }
    }
}

function startGame() {
    score = 0;
    if (scoreDisplay) scoreDisplay.textContent = score;

    if (recognizer) {
        // This logic was correct for the TARGET, but the fix above handles the LISTENING.
        availableChords = recognizer.wordLabels().filter(l => 
            l !== "_background_noise_" && l !== "_unknown_"
        );
    }
    nextRound();
}

function nextRound() {
    const randomIndex = Math.floor(Math.random() * availableChords.length);
    currentTarget = availableChords[randomIndex];

    if (targetDisplay) targetDisplay.textContent = currentTarget;
    if (successOverlay) successOverlay.classList.add("opacity-0"); 
    isRoundActive = true;
}

function triggerSuccess() {
    isRoundActive = false;
    score++;
    if (scoreDisplay) scoreDisplay.textContent = score;
    if (successOverlay) successOverlay.classList.remove("opacity-0");
    setTimeout(nextRound, 1500);
}