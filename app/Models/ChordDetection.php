<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChordDetection extends Model
{
    use HasFactory;

    protected $fillable = [
        'practice_session_id',
        'chord_name',
        'confidence',
        'detected_at',
    ];

    protected $casts = [
        'detected_at' => 'datetime',
        'confidence' => 'decimal:2',
    ];

    public function practiceSession()
    {
        return $this->belongsTo(PracticeSession::class);
    }
}