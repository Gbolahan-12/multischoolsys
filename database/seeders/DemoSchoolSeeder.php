<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;

class DemoSchoolSeeder extends Seeder
{
    public function run()
    {
        // ------------------------
        // 1️⃣ Create Schools
        // ------------------------
        $primary = School::create([
            'name' => 'Sunrise Primary School',
            'address' => '123 Primary Rd',
        ]);

        $secondary = School::create([
            'name' => 'Sunrise Secondary School',
            'address' => '456 Secondary Ave',
        ]);

        // ------------------------
        // 2️⃣ Create Classes
        // ------------------------
        $primaryClasses = ['Primary 1', 'Primary 2', 'Primary 3'];
        foreach ($primaryClasses as $cls) {
            $primaryClass = $primary->classes()->create([
                'name' => $cls,
            ]);
        }

        $secondaryClasses = ['JSS 1', 'JSS 2', 'SSS 1', 'SSS 2'];
        foreach ($secondaryClasses as $cls) {
            $secondaryClass = $secondary->classes()->create([
                'name' => $cls,
            ]);
        }

        // ------------------------
        // 3️⃣ Create Subjects per school
        // ------------------------
        $primarySubjects = ['Mathematics', 'English', 'Basic Science', 'Social Studies'];
        foreach ($primarySubjects as $sub) {
            $primary->subjects()->create(['name' => $sub]);
        }

        $secondarySubjects = ['Mathematics', 'English', 'Physics', 'Chemistry', 'Biology', 'Economics'];
        foreach ($secondarySubjects as $sub) {
            $secondary->subjects()->create(['name' => $sub]);
        }

        // ------------------------
        // 4️⃣ Create Staff (optional)
        // ------------------------
        User::create([
            'fullname' => 'Alice Staff',
            'email' => 'alice.staff@example.com',
            'password' => bcrypt('password'),
            'role' => 'staff',
            'school_id' => $primary->id,
        ]);

        User::create([
            'fullname' => 'Bob Staff',
            'email' => 'bob.staff@example.com',
            'password' => bcrypt('password'),
            'role' => 'staff',
            'school_id' => $secondary->id,
        ]);

        // ------------------------
        // 5️⃣ Create Students
        // ------------------------
        $primaryStudents = [
            ['name' => 'John Doe', 'email' => 'john.primary@example.com', 'class_name' => 'Primary 1'],
            ['name' => 'Jane Doe', 'email' => 'jane.primary@example.com', 'class_name' => 'Primary 2'],
        ];

        foreach ($primaryStudents as $stu) {
            $cls = $primary->classes()->where('name', $stu['class_name'])->first();
            User::create([
                'fullname' => $stu['name'],
                'email' => $stu['email'],
                'password' => bcrypt('password'),
                'role' => 'student',
                'school_id' => $primary->id,
                'class_id' => $cls->id,
            ]);
        }

        $secondaryStudents = [
            ['name' => 'Tom Secondary', 'email' => 'tom.secondary@example.com', 'class_name' => 'JSS 1'],
            ['name' => 'Lucy Secondary', 'email' => 'lucy.secondary@example.com', 'class_name' => 'SSS 1'],
        ];

        foreach ($secondaryStudents as $stu) {
            $cls = $secondary->classes()->where('name', $stu['class_name'])->first();
            User::create([
                'fullname' => $stu['name'],
                'email' => $stu['email'],
                'password' => bcrypt('password'),
                'role' => 'student',
                'school_id' => $secondary->id,
                'class_id' => $cls->id,
            ]);
        }

        $this->command->info("✅ Demo schools, classes, subjects, staff, and students created!");
    }
}
