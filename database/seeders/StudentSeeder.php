<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Profile;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class StudentSeeder extends Seeder
{
    protected $faker;

    public function run(): void
    {
        $this->faker = Faker::create();
        $studentRole = Role::where('name', 'student')->first();
        $adminUser = User::where('email', 'admin@linconhigh.ac.ug')->first();

        // O-Level Students (S1-S4)
        $this->createStudents('O-Level', 50, $studentRole, $adminUser);

        // A-Level Students (S5-S6)
        $this->createStudents('A-Level', 30, $studentRole, $adminUser);
    }

    private function createStudents($level, $count, $role, $creator)
{
    $this->command->info("Creating {$count} {$level} students...");

    for ($i = 1; $i <= $count; $i++) {
        $admissionYear = '2024';
        $educationLevel = $level === 'O-Level' ? 'O' : 'A';
        $gender = $this->faker->randomElement(['M', 'F']);
        $admissionNumber = str_pad($i, 4, '0', STR_PAD_LEFT);

        // Generate student ID: 23/A/0001/F
        $studentId = sprintf('%s/%s/%s/%s',
            substr($admissionYear, -2),
            $educationLevel,
            $admissionNumber,
            $gender
        );

        // Generate email: 23/A/0001/F@lhs.ac.ug
        $email = $studentId . '@lhs.ac.ug';

        $student = User::create([
            'name' => $this->generateStudentName(),
            'email' => $email,
            'password' => Hash::make('student123'),
            'is_active' => true,

        ]);

        // Attach student role
        $student->roles()->attach($role->id);

        // Create Student Profile
        Profile::create([
            'user_id' => $student->id,
            'admission_year' => $admissionYear,
            'student_type' => 'U', // Ugandan
            'education_level' => $educationLevel,
            'admission_number' => $admissionNumber,
            'student_id' => $studentId,
            'gender' => $gender,
            'date_of_birth' => $this->faker->dateTimeBetween('-18 years', '-13 years'),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'parent_name' => $this->faker->name(),
            'parent_phone' => $this->faker->phoneNumber(),
            'parent_email' => $this->faker->safeEmail(),
            'emergency_contact' => $this->faker->name(),
            'emergency_phone' => $this->faker->phoneNumber(),
        ]);
    }

    $this->command->info("Created {$count} {$level} students");
}

    private function generateStudentName()
    {
        $firstNames = ['John', 'Mary', 'David', 'Sarah', 'Michael', 'Grace', 'James', 'Alice', 'Robert', 'Emma', 'Peter', 'Joy'];
        $lastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Miller', 'Davis', 'Garcia', 'Rodriguez', 'Wilson'];

        return $this->faker->randomElement($firstNames) . ' ' . $this->faker->randomElement($lastNames);
    }

    private function generateStudentId($level, $index)
    {
        $prefix = $level === 'O-Level' ? 'OL' : 'AL';
        return $prefix . str_pad($index, 4, '0', STR_PAD_LEFT);
    }
}
