<?php

namespace App\Http\Controllers;

use App\Models\ChordDetection;
use App\Models\PracticeSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PracticeController extends Controller
{
    public function index()
    {
        return view('tutor');
    }

    public function startSession(Request $request)
    {
        $session = PracticeSession::create([
            'user_id' => Auth::id(),
            'started_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'session_id' => $session->id,
        ]);
    }

    public function recordChord(Request $request)
    {
        $validated = $request->validate([
            'session_id' => 'required|exists:practice_sessions,id',
            'chord_name' => 'required|string',
            'confidence' => 'required|numeric|min:0|max:1',
        ]);

        $session = PracticeSession::findOrFail($validated['session_id']);

        if ($session->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        ChordDetection::create([
            'practice_session_id' => $validated['session_id'],
            'chord_name' => $validated['chord_name'],
            'confidence' => $validated['confidence'] * 100,
            'detected_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function endSession(Request $request)
    {
        $validated = $request->validate([
            'session_id' => 'required|exists:practice_sessions,id',
        ]);

        $session = PracticeSession::findOrFail($validated['session_id']);

        if ($session->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $session->ended_at = now();
        $session->duration_seconds = $session->started_at->diffInSeconds($session->ended_at);
        
        $detections = $session->chordDetections;
        $session->total_detections = $detections->count();
        
        if ($session->total_detections > 0) {
            $session->average_confidence = $detections->avg('confidence');
        }
        
        $session->save();

        return response()->json([
            'success' => true,
            'duration' => $session->duration_seconds,
            'total_detections' => $session->total_detections,
            'average_confidence' => round($session->average_confidence, 2),
        ]);
    }
}