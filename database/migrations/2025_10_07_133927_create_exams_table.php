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
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('school_classes')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('instructions')->nullable();
            $table->text('description')->nullable();
            $table->enum('type', ['quiz', 'midterm', 'end_of_term', 'practice', 'mock']);
            $table->integer('duration'); // in minutes
            $table->integer('total_marks');
            $table->integer('passing_marks');
            $table->datetime('start_time');
            $table->datetime('end_time');
            $table->integer('max_attempts')->default(1);
            $table->boolean('randomize_questions')->default(false);
            $table->boolean('require_fullscreen')->default(false);
            $table->boolean('show_results')->default(true);
            $table->timestamp('results_released_at')->nullable();
            $table->boolean('is_published')->default(false);
            $table->boolean('is_archived')->default(false); // Added this line
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
