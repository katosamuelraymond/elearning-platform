<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->datetime('started_at');
            $table->datetime('submitted_at')->nullable();
            $table->integer('time_spent')->default(0); // in seconds
            $table->integer('total_score')->nullable();
            $table->enum('status', ['in_progress', 'submitted', 'graded', 'expired'])->default('in_progress');
            $table->json('answers')->nullable();
            $table->json('manual_grades')->nullable();
            $table->timestamps();

            $table->unique(['exam_id', 'student_id']);
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_attempts');
    }
};
