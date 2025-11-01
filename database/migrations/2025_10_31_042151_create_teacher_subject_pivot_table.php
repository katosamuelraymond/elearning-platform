<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('teacher_subject', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->enum('level', ['o_level', 'a_level', 'both'])->default('o_level');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['teacher_id', 'subject_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('teacher_subject');
    }
};
