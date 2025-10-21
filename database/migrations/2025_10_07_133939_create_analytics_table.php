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
        Schema::create('analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('school_classes')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->decimal('overall_percentage', 5, 2);
            $table->integer('assignments_completed');
            $table->integer('assignments_total');
            $table->integer('exams_completed');
            $table->integer('exams_total');
            $table->integer('quizzes_completed');
            $table->integer('quizzes_total');
            $table->json('weak_areas')->nullable();
            $table->json('strength_areas')->nullable();
            $table->string('academic_term');
            $table->date('calculated_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics');
    }
};
