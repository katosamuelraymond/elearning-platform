<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Academic\SchoolClass;
use App\Models\Academic\Stream;
use App\Models\Academic\Subject;
use App\Models\Academic\AcademicYear;
use App\Models\Academic\Term;

class AcademicStructureSeeder extends Seeder
{
    public function run(): void
    {
        // Create Academic Year
        $academicYear = AcademicYear::create([
            'year' => '2024-2025',
            'start_date' => '2024-02-05',
            'end_date' => '2024-11-29',
            'is_current' => true,
        ]);

        $this->command->info("Created Academic Year: {$academicYear->year}");

        // Create Terms
        $terms = [
            [
                'name' => 'Term 1',
                'term_number' => 1,
                'start_date' => '2024-02-05',
                'end_date' => '2024-05-03',
                'is_current' => true,
            ],
            [
                'name' => 'Term 2',
                'term_number' => 2,
                'start_date' => '2024-05-27',
                'end_date' => '2024-08-23',
                'is_current' => false,
            ],
            [
                'name' => 'Term 3',
                'term_number' => 3,
                'start_date' => '2024-09-09',
                'end_date' => '2024-11-29',
                'is_current' => false,
            ]
        ];

        foreach ($terms as $termData) {
            $termData['academic_year_id'] = $academicYear->id;
            Term::create($termData);
        }

        $this->command->info('Created 3 terms for the academic year');

        // Create School Classes
        $oLevelClasses = [
            ['name' => 'S1', 'description' => 'Senior One - Ordinary Level'],
            ['name' => 'S2', 'description' => 'Senior Two - Ordinary Level'],
            ['name' => 'S3', 'description' => 'Senior Three - Ordinary Level'],
            ['name' => 'S4', 'description' => 'Senior Four - Ordinary Level'],
        ];

        $aLevelClasses = [
            ['name' => 'S5', 'description' => 'Senior Five - Advanced Level'],
            ['name' => 'S6', 'description' => 'Senior Six - Advanced Level'],
        ];

        $classCount = 0;
        $streamCount = 0;

        // Create O-Level Classes and Streams
        foreach ($oLevelClasses as $classData) {
            $classData['level'] = 'O-Level';
            $class = SchoolClass::create($classData);
            $classCount++;

            // Create Streams for O-Level classes (A, B, C)
            foreach (['A', 'B', 'C'] as $streamName) {
                Stream::create([
                    'class_id' => $class->id,
                    'name' => $streamName,
                    'description' => "{$class->name} Stream {$streamName}",
                    'capacity' => 40,
                ]);
                $streamCount++;
            }
        }

        // Create A-Level Classes and Streams
        foreach ($aLevelClasses as $classData) {
            $classData['level'] = 'A-Level';
            $class = SchoolClass::create($classData);
            $classCount++;

            // Create Streams for A-Level classes (Science, Arts, Business)
            foreach (['Science', 'Arts', 'Business'] as $streamName) {
                Stream::create([
                    'class_id' => $class->id,
                    'name' => $streamName,
                    'description' => "{$class->name} {$streamName} Stream",
                    'capacity' => 35, // Smaller classes for A-Level
                ]);
                $streamCount++;
            }
        }

        $this->command->info("Created {$classCount} classes and {$streamCount} streams");

        // Create Subjects
        $subjects = [
            // O-Level Compulsory Subjects
            ['name' => 'Mathematics', 'code' => 'MATH', 'type' => 'compulsory', 'description' => 'Basic Mathematics for O-Level'],
            ['name' => 'English Language', 'code' => 'ENG', 'type' => 'compulsory', 'description' => 'English Language and Communication'],
            ['name' => 'Physics', 'code' => 'PHY', 'type' => 'compulsory', 'description' => 'Physics for Ordinary Level'],
            ['name' => 'Chemistry', 'code' => 'CHEM', 'type' => 'compulsory', 'description' => 'Chemistry for Ordinary Level'],
            ['name' => 'Biology', 'code' => 'BIO', 'type' => 'compulsory', 'description' => 'Biology for Ordinary Level'],
            ['name' => 'History', 'code' => 'HIST', 'type' => 'compulsory', 'description' => 'History of East Africa and World'],
            ['name' => 'Geography', 'code' => 'GEO', 'type' => 'compulsory', 'description' => 'Physical and Human Geography'],
            ['name' => 'Christian Religious Education', 'code' => 'CRE', 'type' => 'compulsory', 'description' => 'Christian Religious Education'],
            ['name' => 'Entrepreneurship Education', 'code' => 'ENT', 'type' => 'compulsory', 'description' => 'Entrepreneurship and Business Skills'],

            // O-Level Optional Subjects
            ['name' => 'Agriculture', 'code' => 'AGR', 'type' => 'optional', 'description' => 'Agriculture Science'],
            ['name' => 'Computer Studies', 'code' => 'COMP', 'type' => 'optional', 'description' => 'Computer and Information Technology'],
            ['name' => 'Commerce', 'code' => 'COMM', 'type' => 'optional', 'description' => 'Commerce and Business Studies'],
            ['name' => 'Literature in English', 'code' => 'LIT', 'type' => 'optional', 'description' => 'Literature in English'],

            // A-Level Science Subjects
            ['name' => 'Advanced Mathematics', 'code' => 'AMATH', 'type' => 'optional', 'description' => 'Pure Mathematics, Applied Mathematics'],
            ['name' => 'Physics (Advanced)', 'code' => 'PHYA', 'type' => 'optional', 'description' => 'Advanced Level Physics'],
            ['name' => 'Chemistry (Advanced)', 'code' => 'CHEMA', 'type' => 'optional', 'description' => 'Advanced Level Chemistry'],
            ['name' => 'Biology (Advanced)', 'code' => 'BIOA', 'type' => 'optional', 'description' => 'Advanced Level Biology'],

            // A-Level Arts Subjects
            ['name' => 'Literature in English (Advanced)', 'code' => 'LITA', 'type' => 'optional', 'description' => 'Advanced Level Literature'],
            ['name' => 'History (Advanced)', 'code' => 'HISTA', 'type' => 'optional', 'description' => 'Advanced Level History'],
            ['name' => 'Geography (Advanced)', 'code' => 'GEOA', 'type' => 'optional', 'description' => 'Advanced Level Geography'],
            ['name' => 'Economics', 'code' => 'ECON', 'type' => 'optional', 'description' => 'Economics Principles and Applications'],
            ['name' => 'Divinity', 'code' => 'DIV', 'type' => 'optional', 'description' => 'Divinity and Religious Studies'],
            ['name' => 'Fine Art', 'code' => 'ART', 'type' => 'optional', 'description' => 'Fine Art and Design'],
        ];

        $subjectCount = 0;
        foreach ($subjects as $subject) {
            Subject::create($subject);
            $subjectCount++;
        }

        $this->command->info("Created {$subjectCount} subjects");

        // Create Class-Subject Relationships
        $this->createClassSubjectRelationships();

        $this->command->info('Academic structure seeding completed successfully!');
    }

    /**
     * Create relationships between classes and subjects
     */

    /**
 * Create relationships between classes and subjects
 */
private function createClassSubjectRelationships()
{
    // Clear existing relationships first
    \DB::table('class_subject')->truncate();

    $oLevelClasses = SchoolClass::where('level', 'O-Level')->get();
    $aLevelClasses = SchoolClass::where('level', 'A-Level')->get();

    // Simple assignment - all O-Level classes get all O-Level subjects
    $oLevelSubjects = Subject::whereIn('code', [
        'MATH', 'ENG', 'PHY', 'CHEM', 'BIO', 'HIST', 'GEO', 'CRE', 'ENT',
        'AGR', 'COMP', 'COMM', 'LIT'
    ])->get();

    $aLevelScienceSubjects = Subject::whereIn('code', ['AMATH', 'PHYA', 'CHEMA', 'BIOA'])->get();
    $aLevelArtsSubjects = Subject::whereIn('code', ['LITA', 'HISTA', 'GEOA', 'ECON', 'DIV', 'ART'])->get();

    // Assign O-Level subjects
    foreach ($oLevelClasses as $class) {
        foreach ($oLevelSubjects as $subject) {
            $class->subjects()->attach($subject->id, ['periods_per_week' => 5]);
        }
    }

    // Assign A-Level subjects based on class name
    foreach ($aLevelClasses as $class) {
        if ($class->name === 'S5' || $class->name === 'S6') {
            // Science combination
            foreach ($aLevelScienceSubjects as $subject) {
                $class->subjects()->attach($subject->id, ['periods_per_week' => 6]);
            }
            // Arts combination
            foreach ($aLevelArtsSubjects as $subject) {
                $class->subjects()->attach($subject->id, ['periods_per_week' => 5]);
            }
        }
    }

    $this->command->info('Created class-subject relationships');
}
}
