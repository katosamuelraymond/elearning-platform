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
        Schema::table('assignments', function (Blueprint $table) {
            $table->string('assignment_file')->nullable()->after('max_file_size');
            $table->string('original_filename')->nullable()->after('assignment_file');
            $table->integer('file_size')->nullable()->after('original_filename');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->dropColumn(['assignment_file', 'original_filename', 'file_size']);
        });
    }
};
