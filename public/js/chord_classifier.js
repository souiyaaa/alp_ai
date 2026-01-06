// --- Configuration ---
const modelPath = "/chords/"; 
const modelURL = modelPath + "model.json";
const metadataURL = modelPath + "metadata.json";

// --- DOM Elements ---
const webcamElement = document.getElementById("webcam"); 
const chordResult = document.getElementById("chord-result"); 
const startBtn = document.getElementById("start-btn");
const endBtn = document.getElementById("end-btn");
const timerDisplay = document.getElementById("timer-display");
const timerElement = document.getElementById("timer");
const sessionStats = document.getElementById("session-stats");
const statDetections = document.getElementById("stat-detections");
const statConfidence = document.getElementById("stat-confidence");

// --- Variables ---
let model, maxPredictions;
let isPredicting = false;
let sessionId = null;
let sessionStartTime = null;
let timerInterval = null;
let detectionCount = 0;
let totalConfidence = 0;
let lastRecordedChord = null;
let recordCooldown = false;

// --- CSRF Token ---
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

// --- Session Management ---
async function startSession() {
    try {
        const response = await fetch('/api/session/start', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            sessionId = data.session_id;
            sessionStartTime = Date.now();
            detectionCount = 0;
            totalConfidence = 0;
            
            startBtn.disabled = true;
            endBtn.disabled = false;
            timerElement.classList.remove('hidden');
            sessionStats.classList.remove('hidden');
            
            startTimer();
            
            console.log("Session started:", sessionId);
        }
    } catch (error) {
        console.error("Error starting session:", error);
        alert("Failed to start session");
    }
}

async function endSession() {
    if (!sessionId) return;
    
    try {
        const response = await fetch('/api/session/end', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ session_id: sessionId })
        });
        
        const data = await response.json();
        
        if (data.success) {
            clearInterval(timerInterval);
            
            const minutes = Math.floor(data.duration / 60);
            const seconds = data.duration % 60;
            
            alert(`Session completed!\n\nDuration: ${minutes}m ${seconds}s\nChords Detected: ${data.total_detections}\nAverage Confidence: ${data.average_confidence}%`);
            
            sessionId = null;
            startBtn.disabled = false;
            endBtn.disabled = true;
            timerElement.classList.add('hidden');
            
            console.log("Session ended:", data);
        }
    } catch (error) {
        console.error("Error ending session:", error);
        alert("Failed to end session");
    }
}

async function recordChord(chordName, confidence) {
    if (!sessionId || recordCooldown) return;
    
    // Prevent recording the same chord too frequently
    recordCooldown = true;
    setTimeout(() => { recordCooldown = false; }, 2000);
    
    try {
        const response = await fetch('/api/session/record', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                session_id: sessionId,
                chord_name: chordName,
                confidence: confidence
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            detectionCount++;
            totalConfidence += (confidence * 100);
            
            statDetections.textContent = detectionCount;
            statConfidence.textContent = Math.round(totalConfidence / detectionCount) + '%';
        }
    } catch (error) {
        console.error("Error recording chord:", error);
    }
}

function startTimer() {
    timerInterval = setInterval(() => {
        const elapsed = Math.floor((Date.now() - sessionStartTime) / 1000);
        const minutes = Math.floor(elapsed / 60).toString().padStart(2, '0');
        const seconds = (elapsed % 60).toString().padStart(2, '0');
        timerDisplay.textContent = `${minutes}:${seconds}`;
    }, 1000);
}

// --- Event Listeners ---
startBtn.addEventListener('click', startSession);
endBtn.addEventListener('click', endSession);

// --- INITIALIZATION ---
async function init() {
    try {
        console.log("DEBUG: Starting Manual Initialization...");

        // 1. Load the model
        model = await tmImage.load(modelURL, metadataURL);
        maxPredictions = model.getTotalClasses();
        console.log("DEBUG: Model loaded.");

        // 2. MANUAL CAMERA SETUP
        const constraints = {
            audio: false,
            video: {
                width: 320,
                height: 240,
                facingMode: "user"
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
        
        webcamElement.onloadedmetadata = () => {
            webcamElement.play();
            console.log("DEBUG: Video is playing.");
            isPredicting = true;
            window.requestAnimationFrame(loop);
        };

        chordResult.textContent = 'Ready! Start a session.';

    } catch (error) {
        console.error("DEBUG ERROR: Init failed.", error);
        chordResult.textContent = 'Error: Check console.';
    }
}

// --- CLASSIFICATION LOOP ---
async function loop() {
    if (isPredicting) {
        await predict();
        window.requestAnimationFrame(loop);
    }
}

// --- PREDICTION FUNCTION ---
async function predict() {
    if (!model || !webcamElement) return;

    const prediction = await model.predict(webcamElement);

    let highestConfidence = 0;
    let predictedClass = '...';

    for (let i = 0; i < maxPredictions; i++) {
        const classPrediction = prediction[i];
        const confidence = classPrediction.probability;
        
        if (confidence > highestConfidence) {
            highestConfidence = confidence;
            predictedClass = classPrediction.className;
        }
    }

    // Threshold check
    if (highestConfidence > 0.75) { 
        chordResult.textContent = predictedClass;
        chordResult.classList.remove("text-gray-400");
        chordResult.classList.add("text-primary-blue");
        
        // Record chord if in active session and different from last
        if (sessionId && predictedClass !== lastRecordedChord) {
            recordChord(predictedClass, highestConfidence);
            lastRecordedChord = predictedClass;
        }
    } else {
        chordResult.textContent = '...';
        chordResult.classList.remove("text-primary-blue");
        chordResult.classList.add("text-gray-400");
    }
}

// Start immediately
window.onload = init;