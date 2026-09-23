<?php

namespace Tests\Feature;

use App\Models\Child;
use App\Models\Guardian;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\StaffMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RosterTest extends TestCase
{
    use RefreshDatabase;

    public function test_roster_preview_shows_school_classes_children_guardians_and_staff(): void
    {
        $school = School::factory()->create([
            'name' => 'Little Seeds Preschool',
            'slug' => 'little-seeds-preschool',
            'address' => 'Quezon City, Metro Manila',
        ]);

        $class = SchoolClass::factory()->create([
            'school_id' => $school->id,
            'name' => 'Sampaguita',
            'age_group' => 'Nursery',
            'room' => 'Room 1',
        ]);

        $teacher = StaffMember::factory()->create([
            'school_id' => $school->id,
            'name' => 'Teacher Ana Cruz',
            'email' => 'teacher.ana@sibol.test',
        ]);

        $class->staffMembers()->attach($teacher, ['assignment_role' => 'lead']);

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

        $this->actingAs($this->userWithRole($school, 'administrator'))
            ->get('/roster')
            ->assertOk()
            ->assertSee('Little Seeds Preschool')
            ->assertSee('Sampaguita')
            ->assertSee('Teacher Ana Cruz')
            ->assertSee('Maya Dela Cruz')
            ->assertSee('Guardian: Rose Dela Cruz')
            ->assertSee('Preview roster only');
    }

    public function test_teacher_roster_only_shows_assigned_classes(): void
    {
        $school = School::factory()->create([
            'name' => 'Little Seeds Preschool',
            'slug' => 'little-seeds-preschool',
        ]);

        $sampaguita = SchoolClass::factory()->create([
            'school_id' => $school->id,
            'name' => 'Sampaguita',
        ]);
        $mango = SchoolClass::factory()->create([
            'school_id' => $school->id,
            'name' => 'Mango',
        ]);

        $teacher = StaffMember::factory()->create([
            'school_id' => $school->id,
            'name' => 'Teacher Ana Cruz',
            'email' => 'teacher.ana@sibol.test',
            'role' => 'teacher',
        ]);
        $sampaguita->staffMembers()->attach($teacher, ['assignment_role' => 'lead']);

        Child::factory()->create([
            'school_id' => $school->id,
            'school_class_id' => $sampaguita->id,
            'preferred_name' => 'Maya',
            'last_name' => 'Dela Cruz',
        ]);
        Child::factory()->create([
            'school_id' => $school->id,
            'school_class_id' => $mango->id,
            'preferred_name' => 'Lia',
            'last_name' => 'Santos',
        ]);

        $this->actingAs($this->userWithRole($school, 'teacher', $teacher))
            ->get('/roster')
            ->assertOk()
            ->assertSee('Sampaguita')
            ->assertSee('Maya Dela Cruz')
            ->assertDontSee('Mango')
            ->assertDontSee('Lia Santos');
    }

    public function test_roster_preview_fails_closed_without_the_demo_school(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/roster')
            ->assertForbidden();

        $this->actingAs($this->userWithRole(School::factory()->create(), 'teacher'))
            ->get('/roster')
            ->assertNotFound();
    }

    public function test_roster_requires_staff_access(): void
    {
        $school = School::factory()->create(['slug' => 'little-seeds-preschool']);

        $this->get('/roster')->assertRedirect('/login');

        $this->actingAs($this->userWithRole($school, 'guardian'))
            ->get('/roster')
            ->assertForbidden();
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
