## 📖 Executive Summary

**AI Smart Guitar Assistant** is a cutting-edge web application designed to revolutionize music education through the power of **Browser-Based Machine Learning**. 

Unlike traditional static learning tools, this application leverages **TensorFlow.js** to perform real-time, client-side inference. It features a dual-modal architecture that can "see" hand positions via webcam (Computer Vision) and "hear" chord tonality via microphone (Audio Signal Processing), providing instant feedback to the user without sending sensitive media data to a server.

---

## 🚀 Key Features

### 1. 👁️ Visual Chord Classifier (Computer Vision Mode)
* **Technology:** Convolutional Neural Networks (CNN) via Teachable Machine Image Model.
* **Functionality:** Uses the user's webcam to analyze hand positioning and finger placement on the guitar fretboard in real-time.
* **Performance:** Optimized for low-latency inference (60 FPS) to provide immediate visual confirmation of the chord being held.
* **Privacy-First:** All video processing happens locally in the browser; no video streams are ever uploaded.

### 2. 🎙️ Audio Chord Trainer (Gamified Audio Mode)
* **Technology:** Fast Fourier Transform (FFT) & Spectrogram Analysis via TensorFlow Speech Commands.
* **Functionality:** A gamified "Flashcard" mode where the AI challenges the user to play specific chords. It listens to the audio waveform, filters background noise, and validates the played chord with high-precision confidence scoring.
* **Real-Time Visualization:** Includes a dynamic confidence graph that visualizes the model's probability distribution across all trained classes (C, Dm, Em, G, etc.) as you play.

---

## 🛠️ Technical Architecture

This project utilizes a modern, hybrid architecture combining a robust PHP backend with a reactive AI-driven frontend.

### Backend (Laravel Framework)
* **Routing & View Management:** Handles the delivery of optimized Blade templates.
* **Asset Management:** efficiently serves the heavy AI model weights (`.bin` files) and metadata via the public directory structure.

### Frontend (The "Brain")
* **TensorFlow.js:** The core engine running the neural networks directly in the browser.
* **WebRTC:** manages secure access to hardware streams (User Media: Camera & Microphone).
* **Tailwind CSS:** Provides a responsive, modern UI that adapts to the state of the AI (e.g., dynamic success overlays, pulsing listening indicators).

### AI Model Management
The project implements a **Modular Model Storage** strategy to handle different data modalities:
* `/public/chords/` → Stores the **Image Classification** model (Vision).
* `/public/chords_audio/` → Stores the **Audio Classification** model (Sound).

---

## 💻 Installation & Setup

1.  **Clone the Repository**
    ```bash
    git clone [https://github.com/yourusername/alp-ai.git](https://github.com/yourusername/alp-ai.git)
    cd alp-ai
    ```

2.  **Install Backend Dependencies**
    ```bash
    composer install
    ```

3.  **Setup Environment**
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4.  **Run the Application**
    * If using **Laravel Herd** (Recommended): simply open the site via `http://alp_ai.test`
    * Manual Serve:
        ```bash
        php artisan serve
        ```

---

## 🎮 How to Use

### Mode 1: Free Play (Visual)
1.  Navigate to **Home** or `/chord-classifier`.
2.  Allow camera permissions when prompted.
3.  Hold a chord (C, G, Dm, or Em) in front of the camera.
4.  The AI will instantly detect and display the chord name on the screen.

### Mode 2: Mini-Game (Audio)
1.  Navigate to **Game Mode** or `/chord-game`.
2.  Click **"Enable Mic & Start"** to initialize the Audio Context.
3.  **The Challenge:** The screen will display a target chord (e.g., "STRUM THIS: G Major").
4.  **The Action:** Strum the chord on your guitar.
5.  **The Verification:** The AI analyzes the sound frequency. If it matches the target with >85% confidence, you score a point and advance to the next round!

---

## 🧠 Why This Matters (Technical Highlight)

This project demonstrates the practical application of **Edge AI**. By moving the inference task from the server to the client (the browser):
1.  **Zero Latency:** Feedback is instantaneous, which is critical for musical timing.
2.  **Scalability:** The server does not need expensive GPUs to process video/audio; the user's device handles the computation.
3.  **Accessibility:** Makes advanced music tutoring technology accessible to anyone with a web browser.

---

*Built with ❤️ by Surya for ALP Project.*