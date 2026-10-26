<?php

namespace Tests\Feature;

use App\Models\Child;
use App\Models\Guardian;
use App\Models\JournalEntry;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\StaffMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DailyUpdatesTest extends TestCase
{
    use RefreshDatabase;

    public function test_parent_home_shows_only_a_linked_child_status_and_journal(): void
    {
        [$school, $class, $child, $guardian, $teacherUser] = $this->createDemoClassroom();
        $parent = $this->userWithRole($school, 'guardian', $guardian);

        JournalEntry::factory()->create([
            'school_id' => $school->id,
            'school_class_id' => $class->id,
            'child_id' => $child->id,
            'author_id' => $teacherUser->id,
            'title' => 'A little artist at work',
            'body' => 'Maya explored colors today.',
            'occurred_at' => now('Asia/Manila')->setTime(9, 30),
        ]);

        $this->actingAs($parent)
            ->get(route('children.show', $child))
            ->assertOk()
            ->assertSee('Maya’s day')
            ->assertSee('Sampaguita class')
            ->assertSee('A little artist at work')
            ->assertSee('Maya explored colors today.');

        $otherChild = Child::factory()->create([
            'school_id' => $school->id,
            'school_class_id' => $class->id,
            'preferred_name' => 'Nico',
        ]);

        $this->actingAs($parent)
            ->get(route('children.show', $otherChild))
            ->assertForbidden();
    }

    public function test_teacher_can_post_a_learning_moment_with_a_private_photo(): void
    {
        Storage::fake('local');
        [$school, $class, $child, , $teacherUser] = $this->createDemoClassroom();

        $photo = UploadedFile::fake()->image('painting.jpg', 640, 480);

        $this->actingAs($teacherUser)
            ->post(route('daily-updates.store'), [
                'child_id' => $child->id,
                'category' => JournalEntry::LearningMoment,
                'title' => 'A little artist at work',
                'body' => 'Maya mixed yellow and green during art time.',
                'occurred_at' => now('Asia/Manila')->format('Y-m-d\TH:i'),
                'photo' => $photo,
            ])->assertRedirect()
            ->assertSessionHas('daily_update_status');

        $entry = JournalEntry::query()->firstOrFail();

        $this->assertSame($school->id, $entry->school_id);
        $this->assertSame($class->id, $entry->school_class_id);
        $this->assertSame($teacherUser->id, $entry->author_id);
        $this->assertNotNull($entry->photo_path);
        Storage::disk('local')->assertExists($entry->photo_path);

        $this->actingAs($teacherUser)
            ->get(route('daily-updates.photo', $entry))
            ->assertOk();
    }

    public function test_teacher_can_log_a_meal_care_entry_with_amount(): void
    {
        [$school, , $child, , $teacherUser] = $this->createDemoClassroom();

        $this->actingAs($teacherUser)
            ->post(route('daily-updates.store'), [
                'child_id' => $child->id,
                'category' => JournalEntry::Meal,
                'title' => 'Merienda finished',
                'body' => 'Banana and pandesal were offered with water.',
                'meal_amount' => 'All finished',
                'occurred_at' => now('Asia/Manila')->format('Y-m-d\TH:i'),
            ])->assertRedirect();

        $this->assertDatabaseHas('journal_entries', [
            'school_id' => $school->id,
            'child_id' => $child->id,
            'category' => JournalEntry::Meal,
            'title' => 'Merienda finished',
            'meal_amount' => 'All finished',
        ]);
    }

    public function test_meal_updates_require_a_meal_amount(): void
    {
        [, , $child, , $teacherUser] = $this->createDemoClassroom();

        $this->actingAs($teacherUser)
            ->from(route('daily-updates.index'))
            ->post(route('daily-updates.store'), [
                'child_id' => $child->id,
                'category' => JournalEntry::Meal,
                'title' => 'Merienda finished',
                'body' => 'Banana and pandesal were offered with water.',
                'occurred_at' => now('Asia/Manila')->format('Y-m-d\TH:i'),
            ])->assertRedirect(route('daily-updates.index'))
            ->assertSessionHasErrors('meal_amount');
    }

    public function test_teacher_cannot_post_for_an_unassigned_class(): void
    {
        [$school, , , , $teacherUser] = $this->createDemoClassroom();
        $otherClass = SchoolClass::factory()->create(['school_id' => $school->id, 'name' => 'Mango']);
        $otherChild = Child::factory()->create([
            'school_id' => $school->id,
            'school_class_id' => $otherClass->id,
            'preferred_name' => 'Lia',
        ]);

        $this->actingAs($teacherUser)
            ->post(route('daily-updates.store'), [
                'child_id' => $otherChild->id,
                'category' => JournalEntry::Activity,
                'title' => 'Garden walk',
                'body' => 'Lia watered the herbs.',
                'occurred_at' => now('Asia/Manila')->format('Y-m-d\TH:i'),
            ])->assertNotFound();
    }

    public function test_guardian_cannot_open_teacher_daily_update_screen(): void
    {
        [$school, , , $guardian] = $this->createDemoClassroom();
        $parent = $this->userWithRole($school, 'guardian', $guardian);

        $this->actingAs($parent)
            ->get(route('daily-updates.index'))
            ->assertForbidden();
    }

    /**
     * @return array{0: School, 1: SchoolClass, 2: Child, 3: Guardian, 4: User}
     */
    private function createDemoClassroom(): array
    {
        $school = School::factory()->create([
            'name' => 'Little Seeds Preschool',
            'slug' => 'little-seeds-preschool',
            'timezone' => 'Asia/Manila',
        ]);

        $class = SchoolClass::factory()->create([
            'school_id' => $school->id,
            'name' => 'Sampaguita',
            'age_group' => 'Nursery',
        ]);

        $staff = StaffMember::factory()->create([
            'school_id' => $school->id,
            'name' => 'Teacher Ana Cruz',
            'email' => 'teacher.ana@sibol.test',
        ]);

        $class->staffMembers()->attach($staff, ['assignment_role' => 'lead']);

        $teacherUser = $this->userWithRole($school, 'teacher', $staff);

        $child = Child::factory()->create([
            'school_id' => $school->id,
            'school_class_id' => $class->id,
            'first_name' => 'Maya',
            'last_name' => 'Dela Cruz',
            'preferred_name' => 'Maya',
        ]);

        $guardian = Guardian::factory()->create([
            'school_id' => $school->id,
            'first_name' => 'Rose',
            'last_name' => 'Dela Cruz',
            'email' => 'rose.delacruz@sibol.test',
        ]);

        $child->guardians()->attach($guardian, [
            'relationship' => 'Mother',
            'is_primary' => true,
            'can_pick_up' => true,
        ]);

        return [$school, $class, $child, $guardian, $teacherUser];
    }

    private function userWithRole(School $school, string $role, mixed $source): User
    {
        $user = User::factory()->create([
            'name' => match ($role) {
                'teacher' => 'Teacher Ana Cruz',
                'guardian' => 'Rose Dela Cruz',
                default => 'Demo User',
            },
            'email' => match ($role) {
                'teacher' => 'teacher.ana@sibol.test',
                'guardian' => 'rose.delacruz@sibol.test',
                default => fake()->safeEmail(),
            },
        ]);

        $user->schoolMemberships()->create([
            'school_id' => $school->id,
            'role' => $role,
            'status' => 'active',
            'source_type' => $source::class,
            'source_id' => $source->id,
        ]);

        return $user;
    }
}
