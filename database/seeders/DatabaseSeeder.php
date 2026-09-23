<?php

namespace Database\Seeders;

use App\Models\AttendanceRecord;
use App\Models\Child;
use App\Models\ChildMessage;
use App\Models\ClassCalendarEvent;
use App\Models\Guardian;
use App\Models\JournalEntry;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\SchoolUserMembership;
use App\Models\StaffMember;
use App\Models\User;
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

        $demoUsers = [
            [
                'email' => 'admin.mia@sibol.test',
                'name' => 'Mia Reyes',
                'role' => 'administrator',
                'source' => $adminMia,
            ],
            [
                'email' => 'teacher.ana@sibol.test',
                'name' => 'Teacher Ana Cruz',
                'role' => 'teacher',
                'source' => $teacherAna,
            ],
            [
                'email' => 'rose.delacruz@sibol.test',
                'name' => 'Rose Dela Cruz',
                'role' => 'guardian',
                'source' => $rose,
            ],
        ];

        foreach ($demoUsers as $demoUser) {
            $user = User::query()->updateOrCreate(
                ['email' => $demoUser['email']],
                [
                    'name' => $demoUser['name'],
                    'password' => null,
                    'email_verified_at' => now(),
                ],
            );

            SchoolUserMembership::query()->updateOrCreate(
                [
                    'user_id' => $user->id,
                    'school_id' => $school->id,
                    'role' => $demoUser['role'],
                ],
                [
                    'status' => 'active',
                    'source_type' => $demoUser['source']::class,
                    'source_id' => $demoUser['source']->id,
                ],
            );
        }

        $today = now('Asia/Manila')->startOfDay();

        ClassCalendarEvent::query()->updateOrCreate(
            ['school_class_id' => $sampaguita->id, 'title' => 'Sampaguita art day'],
            [
                'school_id' => $school->id,
                'description' => 'Children will explore colors and leaf prints. Please send an extra shirt.',
                'event_type' => ClassCalendarEvent::Activity,
                'event_date' => $today->copy()->next('Wednesday')->toDateString(),
                'starts_at' => $today->copy()->next('Wednesday')->setTime(9, 0),
                'ends_at' => $today->copy()->next('Wednesday')->setTime(10, 0),
            ],
        );

        ClassCalendarEvent::query()->updateOrCreate(
            ['school_class_id' => $sampaguita->id, 'title' => 'Family day practice'],
            [
                'school_id' => $school->id,
                'description' => 'Short classroom practice for the upcoming family day song.',
                'event_type' => ClassCalendarEvent::SchoolEvent,
                'event_date' => $today->copy()->addDays(8)->toDateString(),
                'starts_at' => $today->copy()->addDays(8)->setTime(10, 30),
                'ends_at' => $today->copy()->addDays(8)->setTime(11, 0),
            ],
        );

        ClassCalendarEvent::query()->updateOrCreate(
            ['school_class_id' => $mango->id, 'title' => 'Mango garden walk'],
            [
                'school_id' => $school->id,
                'description' => 'Kinder 1 will visit the garden and water the herbs after morning circle.',
                'event_type' => ClassCalendarEvent::Activity,
                'event_date' => $today->copy()->addDays(3)->toDateString(),
                'starts_at' => $today->copy()->addDays(3)->setTime(9, 15),
                'ends_at' => $today->copy()->addDays(3)->setTime(9, 45),
            ],
        );

        ClassCalendarEvent::query()->updateOrCreate(
            ['school_class_id' => $mango->id, 'title' => 'Bring favorite storybook'],
            [
                'school_id' => $school->id,
                'description' => 'Reminder for show-and-tell. Label the book with the child’s name.',
                'event_type' => ClassCalendarEvent::Reminder,
                'event_date' => $today->copy()->addDays(10)->toDateString(),
                'starts_at' => null,
                'ends_at' => null,
            ],
        );

        AttendanceRecord::query()->updateOrCreate(
            ['child_id' => $maya->id, 'occurred_at' => now('Asia/Manila')->setTime(8, 4)],
            [
                'school_id' => $school->id,
                'school_class_id' => $sampaguita->id,
                'type' => AttendanceRecord::CheckIn,
                'actor_name' => 'Teacher Ana Cruz',
                'note' => 'Demo check-in',
            ],
        );

        $teacherUser = User::query()->where('email', 'teacher.ana@sibol.test')->firstOrFail();

        $parentUser = User::query()->where('email', 'rose.delacruz@sibol.test')->firstOrFail();

        ChildMessage::query()->updateOrCreate(
            ['child_id' => $maya->id, 'body' => 'Good morning! Maya was excited to show her color sorting work today.'],
            [
                'school_id' => $school->id,
                'sender_id' => $teacherUser->id,
                'sender_role' => ChildMessage::Teacher,
                'sent_at' => now('Asia/Manila')->setTime(11, 5),
            ],
        );

        ChildMessage::query()->updateOrCreate(
            ['child_id' => $maya->id, 'body' => 'Thank you, Teacher Ana. Please remind her that Lola will pick her up at 4 PM.'],
            [
                'school_id' => $school->id,
                'sender_id' => $parentUser->id,
                'sender_role' => ChildMessage::Guardian,
                'sent_at' => now('Asia/Manila')->setTime(11, 12),
            ],
        );

        JournalEntry::query()->updateOrCreate(
            ['child_id' => $maya->id, 'title' => 'A little artist at work'],
            [
                'school_id' => $school->id,
                'school_class_id' => $sampaguita->id,
                'author_id' => $teacherUser->id,
                'category' => JournalEntry::LearningMoment,
                'body' => 'Maya explored warm colors during our Sampaguita art table and proudly explained her sun drawing.',
                'meal_amount' => null,
                'occurred_at' => now('Asia/Manila')->setTime(9, 30),
                'status' => 'published',
            ],
        );

        JournalEntry::query()->updateOrCreate(
            ['child_id' => $maya->id, 'title' => 'Merienda finished'],
            [
                'school_id' => $school->id,
                'school_class_id' => $sampaguita->id,
                'author_id' => $teacherUser->id,
                'category' => JournalEntry::Meal,
                'body' => 'Banana and pandesal were offered with water.',
                'meal_amount' => 'All finished',
                'occurred_at' => now('Asia/Manila')->setTime(10, 15),
                'status' => 'published',
            ],
        );
    }
}
