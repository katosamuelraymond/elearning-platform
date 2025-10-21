<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Profile;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Starting database seeding...');

        // Create Roles
        $this->command->info('Creating roles...');
        $adminRole = Role::create(['name' => 'admin', 'description' => 'System Administrator']);
        $teacherRole = Role::create(['name' => 'teacher', 'description' => 'Teaching Staff']);
        $studentRole = Role::create(['name' => 'student', 'description' => 'Student']);

        $this->command->info('Created 3 roles: admin, teacher, student');

        // Create Admin User (NO role column)
        $this->command->info('Creating admin user...');
        $admin = User::create([
            'name' => 'System Administrator',
            'email' => 'admin@linconhigh.ac.ug',
            'password' => Hash::make('admin123'),
            'is_active' => true,

        ]);

        // Attach admin role (using role_user table)
        $admin->roles()->attach($adminRole->id);

        // Create Admin Profile
        Profile::create([
            'user_id' => $admin->id,
            'employee_id' => 'ADM001',
            'phone' => '+256700000001',
            'address' => 'School Administration Building, Lincoln High School',
        ]);

        $this->command->info('Admin user created: admin@linconhigh.ac.ug / admin123');

        // Create Teachers with realistic data (NO role column)
        $this->command->info('Creating teaching staff...');

        $teachersData = [
            ['name' => 'Dr. Robert Mugisha', 'qualification' => 'PhD', 'specialization' => 'Mathematics', 'email' => 'r.mugisha@linconhigh.ac.ug'],
            ['name' => 'Ms. Sarah Nalwoga', 'qualification' => 'M.Ed', 'specialization' => 'Physics', 'email' => 's.nalwoga@linconhigh.ac.ug'],
            ['name' => 'Mr. David Kato', 'qualification' => 'B.Ed', 'specialization' => 'Chemistry', 'email' => 'd.kato@linconhigh.ac.ug'],
            ['name' => 'Mrs. Grace Namuli', 'qualification' => 'M.Ed', 'specialization' => 'Biology', 'email' => 'g.namuli@linconhigh.ac.ug'],
            ['name' => 'Mr. James Ssemakula', 'qualification' => 'B.Ed', 'specialization' => 'English Language', 'email' => 'j.ssemakula@linconhigh.ac.ug'],
            ['name' => 'Ms. Alice Nanteza', 'qualification' => 'M.A', 'specialization' => 'Literature', 'email' => 'a.nanteza@linconhigh.ac.ug'],
            ['name' => 'Mr. Peter Okello', 'qualification' => 'B.Ed', 'specialization' => 'History', 'email' => 'p.okello@linconhigh.ac.ug'],
            ['name' => 'Mrs. Mary Kabaale', 'qualification' => 'M.Ed', 'specialization' => 'Geography', 'email' => 'm.kabaale@linconhigh.ac.ug'],
            ['name' => 'Mr. John Wasswa', 'qualification' => 'B.Sc', 'specialization' => 'Computer Studies', 'email' => 'j.wasswa@linconhigh.ac.ug'],
            ['name' => 'Ms. Prossy Nalubega', 'qualification' => 'B.Ed', 'specialization' => 'Commerce', 'email' => 'p.nalubega@linconhigh.ac.ug'],
        ];

        foreach ($teachersData as $index => $teacherData) {
            $teacherNumber = $index + 1;

            $teacher = User::create([
                'name' => $teacherData['name'],
                'email' => $teacherData['email'],
                'password' => Hash::make('teacher123'),
                'is_active' => true,

            ]);

            // Attach teacher role
            $teacher->roles()->attach($teacherRole->id);

            // Create Teacher Profile
            Profile::create([
                'user_id' => $teacher->id,
                'teacher_id' => 'T' . str_pad($teacherNumber, 3, '0', STR_PAD_LEFT),
                'employee_id' => 'EMP' . str_pad($teacherNumber, 3, '0', STR_PAD_LEFT),
                'qualification' => $teacherData['qualification'],
                'specialization' => $teacherData['specialization'],
                'employment_date' => now()->subYears(rand(1, 10)),
                'phone' => '+2567' . str_pad(rand(1000000, 9999999), 7, '0', STR_PAD_LEFT),
                'address' => fake()->address(),
            ]);
        }

        $this->command->info('Created 10 teaching staff');

        // Create Students
        $this->call(StudentSeeder::class);
        $this->call(AcademicStructureSeeder::class);

        $this->command->info('Database seeding completed successfully!');
    }
}
