<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PracticeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/', [PracticeController::class, 'index']);
    Route::get('/tutor', [PracticeController::class, 'index'])->name('tutor');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    
    // API routes for practice sessions
    Route::post('/api/session/start', [PracticeController::class, 'startSession']);
    Route::post('/api/session/record', [PracticeController::class, 'recordChord']);
    Route::post('/api/session/end', [PracticeController::class, 'endSession']);
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});