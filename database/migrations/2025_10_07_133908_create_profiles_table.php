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
       Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('student_id')->nullable()->unique();
            $table->string('teacher_id')->nullable()->unique();
            $table->string('employee_id')->nullable()->unique();



            $table->string('admission_year')->nullable();
            $table->enum('student_type', ['U', 'F'])->nullable();
            $table->enum('education_level', ['O', 'A'])->nullable();
            $table->string('admission_number')->nullable();


            $table->date('date_of_birth')->nullable();
             $table->enum('gender', ['M', 'F'])->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();


            $table->string('parent_name')->nullable();
            $table->string('parent_phone')->nullable();
            $table->string('parent_email')->nullable();


            $table->string('qualification')->nullable();
            $table->text('specialization')->nullable();


            $table->date('employment_date')->nullable();


            $table->string('emergency_contact')->nullable();
            $table->string('emergency_phone')->nullable();


            $table->text('notes')->nullable();


            $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
