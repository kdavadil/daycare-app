<?php

namespace Tests\Feature;

use App\Models\ClassCalendarEvent;
use App\Models\Guardian;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\StaffMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClassCalendarTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_events_for_all_classes(): void
    {
        [$school, $sampaguita, $mango] = $this->createSchoolWithClasses();
        $admin = $this->userWithRole($school, 'administrator');

        ClassCalendarEvent::factory()->create([
            'school_id' => $school->id,
            'school_class_id' => $sampaguita->id,
            'title' => 'Sampaguita art day',
            'event_date' => '2026-09-24',
            'starts_at' => '2026-09-24 09:00:00',
        ]);
        ClassCalendarEvent::factory()->create([
            'school_id' => $school->id,
            'school_class_id' => $mango->id,
            'title' => 'Mango garden walk',
            'event_date' => '2026-09-25',
            'starts_at' => '2026-09-25 09:15:00',
        ]);

        $this->actingAs($admin)
            ->get(route('class-calendar.index', ['month' => '2026-09']))
            ->assertOk()
            ->assertSee('Class calendar')
            ->assertSee('Sampaguita art day')
            ->assertSee('Mango garden walk')
            ->assertSee('September 2026');
    }

    public function test_teacher_only_sees_assigned_class_events(): void
    {
        [$school, $sampaguita, $mango] = $this->createSchoolWithClasses();
        $teacher = StaffMember::factory()->create([
            'school_id' => $school->id,
            'email' => 'teacher.ana@sibol.test',
        ]);
        $sampaguita->staffMembers()->attach($teacher, ['assignment_role' => 'lead']);
        $teacherUser = $this->userWithRole($school, 'teacher', $teacher);

        ClassCalendarEvent::factory()->create([
            'school_id' => $school->id,
            'school_class_id' => $sampaguita->id,
            'title' => 'Sampaguita art day',
            'event_date' => '2026-09-24',
        ]);
        ClassCalendarEvent::factory()->create([
            'school_id' => $school->id,
            'school_class_id' => $mango->id,
            'title' => 'Mango garden walk',
            'event_date' => '2026-09-25',
        ]);

        $this->actingAs($teacherUser)
            ->get(route('class-calendar.index', ['month' => '2026-09']))
            ->assertOk()
            ->assertSee('Sampaguita art day')
            ->assertDontSee('Mango garden walk');
    }

    public function test_guardians_cannot_view_class_calendar(): void
    {
        [$school] = $this->createSchoolWithClasses();
        $guardian = Guardian::factory()->create(['school_id' => $school->id]);
        $parent = $this->userWithRole($school, 'guardian', $guardian);

        $this->actingAs($parent)
            ->get(route('class-calendar.index'))
            ->assertForbidden();
    }

    public function test_guest_is_redirected_from_class_calendar(): void
    {
        $this->get(route('class-calendar.index'))
            ->assertRedirect(route('login', absolute: false));
    }

    /**
     * @return array{0: School, 1: SchoolClass, 2: SchoolClass}
     */
    private function createSchoolWithClasses(): array
    {
        $school = School::factory()->create([
            'name' => 'Little Seeds Preschool',
            'slug' => 'little-seeds-preschool',
            'timezone' => 'Asia/Manila',
        ]);

        $sampaguita = SchoolClass::factory()->create([
            'school_id' => $school->id,
            'name' => 'Sampaguita',
            'age_group' => 'Nursery',
        ]);
        $mango = SchoolClass::factory()->create([
            'school_id' => $school->id,
            'name' => 'Mango',
            'age_group' => 'Kinder 1',
        ]);

        return [$school, $sampaguita, $mango];
    }

    private function userWithRole(School $school, string $role, mixed $source = null): User
    {
        $user = User::factory()->create();

        $user->schoolMemberships()->create([
            'school_id' => $school->id,
            'role' => $role,
            'status' => 'active',
            'source_type' => $source ? $source::class : null,
            'source_id' => $source?->id,
        ]);

        return $user;
    }
}
