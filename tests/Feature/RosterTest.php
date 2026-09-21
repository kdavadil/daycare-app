<?php

namespace Tests\Feature;

use App\Models\Child;
use App\Models\Guardian;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\StaffMember;
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

        $this->get('/roster')
            ->assertOk()
            ->assertSee('Little Seeds Preschool')
            ->assertSee('Sampaguita')
            ->assertSee('Teacher Ana Cruz')
            ->assertSee('Maya Dela Cruz')
            ->assertSee('Guardian: Rose Dela Cruz')
            ->assertSee('Preview roster only');
    }

    public function test_roster_preview_fails_closed_without_the_demo_school(): void
    {
        $this->get('/roster')->assertNotFound();
    }
}
