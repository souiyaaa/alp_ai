<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('practice_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->integer('duration_seconds')->default(0);
            $table->integer('total_detections')->default(0);
            $table->decimal('average_confidence', 5, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('chord_detections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('practice_session_id')->constrained()->onDelete('cascade');
            $table->string('chord_name');
            $table->decimal('confidence', 5, 2);
            $table->timestamp('detected_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chord_detections');
        Schema::dropIfExists('practice_sessions');
    }
};