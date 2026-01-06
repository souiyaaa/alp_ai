<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $sessions = $user->practiceSessions()
            ->with('chordDetections')
            ->orderBy('started_at', 'desc')
            ->get();

        $stats = [
            'total_sessions' => $sessions->count(),
            'total_practice_time' => $sessions->sum('duration_seconds'),
            'total_chords_detected' => $sessions->sum('total_detections'),
            'average_confidence' => $sessions->avg('average_confidence'),
        ];

        $chartData = $sessions->take(10)->reverse()->map(function ($session) {
            return [
                'date' => $session->started_at->format('M d'),
                'duration' => round($session->duration_seconds / 60, 1), // minutes
                'detections' => $session->total_detections,
                'confidence' => round($session->average_confidence, 1),
            ];
        })->values();

        return view('profile', compact('user', 'sessions', 'stats', 'chartData'));
    }
}