<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subject_combinations', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // PCM, PCB, HEG, LED, BAM, etc.
            $table->string('description'); // Physics-Chemistry-Math, History-Economics-Geography, etc.
            $table->foreignId('class_id')->constrained('school_classes')->onDelete('cascade');
            $table->foreignId('stream_id')->constrained()->onDelete('cascade');
            $table->json('core_subjects'); // [1, 2, 3] - array of subject IDs
            $table->json('optional_subjects')->nullable(); // [4, 5, 6] - array of subject IDs
            $table->json('support_subjects')->nullable(); // [7, 8] - General Paper, etc.
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Unique constraint to prevent duplicate combinations per class/stream
            $table->unique(['class_id', 'stream_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subject_combinations');
    }
};
