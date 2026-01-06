<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PracticeSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'started_at',
        'ended_at',
        'duration_seconds',
        'total_detections',
        'average_confidence',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function chordDetections()
    {
        return $this->hasMany(ChordDetection::class);
    }
}