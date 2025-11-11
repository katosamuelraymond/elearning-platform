<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_class_assignments', function (Blueprint $table) {
            $table->foreignId('combination_id')->nullable()->constrained('subject_combinations')->onDelete('set null');
            // We'll keep optional_subjects for O-Level choices
        });
    }

    public function down(): void
    {
        Schema::table('student_class_assignments', function (Blueprint $table) {
            $table->dropForeign(['combination_id']);
            $table->dropColumn('combination_id');
        });
    }
};
