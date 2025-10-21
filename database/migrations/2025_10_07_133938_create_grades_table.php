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
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('school_classes')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->string('gradeable_type'); // 'assignment', 'exam', 'quiz'
            $table->unsignedBigInteger('gradeable_id');
            $table->decimal('score', 5, 2);
            $table->decimal('max_score', 5, 2);
            $table->decimal('percentage', 5, 2);
            $table->string('letter_grade')->nullable(); // A, B, C, D, F
            $table->text('comments')->nullable();
            $table->foreignId('graded_by')->constrained('users');
            $table->string('academic_term'); // Term 1 2025, Term 2 2025
            $table->timestamps();

            $table->index(['gradeable_type', 'gradeable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
