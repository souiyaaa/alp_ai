// --- Configuration ---
const modelPath = "/chords/"; 
const modelURL = modelPath + "model.json";
const metadataURL = modelPath + "metadata.json";

// --- DOM Elements ---
const webcamElement = document.getElementById("webcam"); 
const chordResult = document.getElementById("chord-result"); 
const labelContainer = document.getElementById("label-container");

// --- Variables ---
let model, maxPredictions;
let isPredicting = false;

// --- INITIALIZATION ---
async function init() {
    try {
        console.log("DEBUG: Starting Manual Initialization...");

        // 1. Load the model
        model = await tmImage.load(modelURL, metadataURL);
        maxPredictions = model.getTotalClasses();
        console.log("DEBUG: Model loaded.");

        // 2. MANUAL CAMERA SETUP (Bypassing tmImage.Webcam)
        // We ask the browser directly for the stream
        const constraints = {
            audio: false,
            video: {
                width: 320,
                height: 240,
                facingMode: "user" // Request front camera
            }
        };

        let stream;
        try {
            stream = await navigator.mediaDevices.getUserMedia(constraints);
            console.log("DEBUG: Raw getUserMedia stream acquired:", stream);
        } catch (err) {
            console.error("DEBUG FATAL: getUserMedia failed.", err);
            chordResult.textContent = `Camera Error: ${err.name}`;
            return;
        }

        // 3. Attach stream to video element
        webcamElement.srcObject = stream;
        
        // Wait for video to load metadata to ensure dimensions are known
        webcamElement.onloadedmetadata = () => {
            webcamElement.play();
            console.log("DEBUG: Video is playing.");
            isPredicting = true;
            window.requestAnimationFrame(loop);
        };

        // 4. Cleanup UI
        if (labelContainer) {
             labelContainer.innerHTML = '';
        }
        chordResult.textContent = 'Active! Show a chord.';

    } catch (error) {
        console.error("DEBUG ERROR: Init failed.", error);
        chordResult.textContent = 'Error: Check console.';
    }
}

// --- CLASSIFICATION LOOP ---
async function loop() {
    if (isPredicting) {
        webcamElement.update; // Force layout update if needed
        await predict();
        window.requestAnimationFrame(loop);
    }
}

// --- PREDICTION FUNCTION ---
async function predict() {
    if (!model || !webcamElement) return;

    // The model.predict() function can take the raw HTMLVideoElement directly!
    // We don't need the tmImage.Webcam wrapper object.
    const prediction = await model.predict(webcamElement);

    let highestConfidence = 0;
    let predictedClass = '...';

    // Loop to find only the MAX confidence chord
    for (let i = 0; i < maxPredictions; i++) {
        const classPrediction = prediction[i];
        const confidence = classPrediction.probability;
        
        if (confidence > highestConfidence) {
            highestConfidence = confidence;
            predictedClass = classPrediction.className;
        }
    }

    // Threshold check (e.g., 75% confidence)
    if (highestConfidence > 0.75) { 
        chordResult.textContent = predictedClass;
        chordResult.classList.remove("text-gray-400");
        chordResult.classList.add("text-primary-blue");
    } else {
        chordResult.textContent = '...';
        chordResult.classList.remove("text-primary-blue");
        chordResult.classList.add("text-gray-400");
    }
}

// Start immediately
window.onload = init;