<?php

namespace Database\Seeders;

use App\Models\Child;
use App\Models\Guardian;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\StaffMember;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $school = School::query()->updateOrCreate(
            ['slug' => 'little-seeds-preschool'],
            [
                'name' => 'Little Seeds Preschool',
                'address' => 'Quezon City, Metro Manila',
                'timezone' => 'Asia/Manila',
            ],
        );

        $sampaguita = SchoolClass::query()->updateOrCreate(
            ['school_id' => $school->id, 'name' => 'Sampaguita'],
            ['age_group' => 'Nursery', 'room' => 'Room 1'],
        );

        $mango = SchoolClass::query()->updateOrCreate(
            ['school_id' => $school->id, 'name' => 'Mango'],
            ['age_group' => 'Kinder 1', 'room' => 'Garden Room'],
        );

        $teacherAna = StaffMember::query()->updateOrCreate(
            ['school_id' => $school->id, 'email' => 'teacher.ana@sibol.test'],
            ['name' => 'Teacher Ana Cruz', 'role' => 'teacher', 'status' => 'active'],
        );

        $teacherBen = StaffMember::query()->updateOrCreate(
            ['school_id' => $school->id, 'email' => 'teacher.ben@sibol.test'],
            ['name' => 'Teacher Ben Santos', 'role' => 'teacher', 'status' => 'active'],
        );

        $adminMia = StaffMember::query()->updateOrCreate(
            ['school_id' => $school->id, 'email' => 'admin.mia@sibol.test'],
            ['name' => 'Mia Reyes', 'role' => 'administrator', 'status' => 'active'],
        );

        $sampaguita->staffMembers()->syncWithoutDetaching([
            $teacherAna->id => ['assignment_role' => 'lead'],
            $adminMia->id => ['assignment_role' => 'support'],
        ]);

        $mango->staffMembers()->syncWithoutDetaching([
            $teacherBen->id => ['assignment_role' => 'lead'],
            $adminMia->id => ['assignment_role' => 'support'],
        ]);

        $maya = Child::query()->updateOrCreate(
            ['school_id' => $school->id, 'first_name' => 'Maya', 'last_name' => 'Dela Cruz'],
            ['school_class_id' => $sampaguita->id, 'preferred_name' => 'Maya', 'birthdate' => '2022-04-12', 'status' => 'active'],
        );

        $nico = Child::query()->updateOrCreate(
            ['school_id' => $school->id, 'first_name' => 'Nico', 'last_name' => 'Garcia'],
            ['school_class_id' => $sampaguita->id, 'preferred_name' => 'Nico', 'birthdate' => '2021-11-03', 'status' => 'active'],
        );

        $lia = Child::query()->updateOrCreate(
            ['school_id' => $school->id, 'first_name' => 'Lia', 'last_name' => 'Santos'],
            ['school_class_id' => $mango->id, 'preferred_name' => 'Lia', 'birthdate' => '2020-08-19', 'status' => 'active'],
        );

        $rose = Guardian::query()->updateOrCreate(
            ['school_id' => $school->id, 'email' => 'rose.delacruz@sibol.test'],
            ['first_name' => 'Rose', 'last_name' => 'Dela Cruz', 'phone' => '+63 917 000 1001'],
        );

        $marco = Guardian::query()->updateOrCreate(
            ['school_id' => $school->id, 'email' => 'marco.garcia@sibol.test'],
            ['first_name' => 'Marco', 'last_name' => 'Garcia', 'phone' => '+63 917 000 1002'],
        );

        $camille = Guardian::query()->updateOrCreate(
            ['school_id' => $school->id, 'email' => 'camille.santos@sibol.test'],
            ['first_name' => 'Camille', 'last_name' => 'Santos', 'phone' => '+63 917 000 1003'],
        );

        $maya->guardians()->syncWithoutDetaching([
            $rose->id => ['relationship' => 'Mother', 'is_primary' => true, 'can_pick_up' => true],
        ]);

        $nico->guardians()->syncWithoutDetaching([
            $marco->id => ['relationship' => 'Father', 'is_primary' => true, 'can_pick_up' => true],
        ]);

        $lia->guardians()->syncWithoutDetaching([
            $camille->id => ['relationship' => 'Mother', 'is_primary' => true, 'can_pick_up' => true],
        ]);
    }
}
