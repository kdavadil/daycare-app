<?php

namespace Tests\Feature;

use App\Models\AttendanceRecord;
use App\Models\Child;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\StaffMember;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_attendance_preview_shows_current_status(): void
    {
        [$school, $class, $child] = $this->createDemoRoster();

        AttendanceRecord::factory()->create([
            'school_id' => $school->id,
            'school_class_id' => $class->id,
            'child_id' => $child->id,
            'type' => AttendanceRecord::CheckIn,
            'occurred_at' => now('Asia/Manila')->setTime(8, 4),
        ]);

        $this->get('/attendance')
            ->assertOk()
            ->assertSee('Check children in and out')
            ->assertSee('Maya Dela Cruz')
            ->assertSee('In school')
            ->assertSee('Checked in');
    }

    public function test_teacher_can_check_a_child_in_and_out_without_duplicate_transitions(): void
    {
        [$school, $class, $child] = $this->createDemoRoster();

        $this->post('/attendance', [
            'child_id' => $child->id,
            'type' => AttendanceRecord::CheckIn,
        ])->assertRedirect();

        $this->assertDatabaseHas('attendance_records', [
            'school_id' => $school->id,
            'school_class_id' => $class->id,
            'child_id' => $child->id,
            'type' => AttendanceRecord::CheckIn,
            'actor_name' => 'Teacher Ana Cruz',
        ]);

        $this->post('/attendance', [
            'child_id' => $child->id,
            'type' => AttendanceRecord::CheckIn,
        ])->assertSessionHas('attendance_error');

        $this->assertSame(1, AttendanceRecord::query()->where('child_id', $child->id)->count());

        $this->post('/attendance', [
            'child_id' => $child->id,
            'type' => AttendanceRecord::CheckOut,
        ])->assertSessionHas('attendance_status');

        $this->assertSame(AttendanceRecord::CheckOut, $child->fresh()->latestAttendanceRecord->type);
    }

    public function test_attendance_rejects_children_outside_the_demo_school(): void
    {
        $this->createDemoRoster();
        $otherSchool = School::factory()->create(['slug' => 'other-school']);
        $otherClass = SchoolClass::factory()->create(['school_id' => $otherSchool->id]);
        $otherChild = Child::factory()->create([
            'school_id' => $otherSchool->id,
            'school_class_id' => $otherClass->id,
        ]);

        $this->post('/attendance', [
            'child_id' => $otherChild->id,
            'type' => AttendanceRecord::CheckIn,
        ])->assertNotFound();
    }

    /**
     * @return array{0: School, 1: SchoolClass, 2: Child}
     */
    private function createDemoRoster(): array
    {
        $school = School::factory()->create([
            'name' => 'Little Seeds Preschool',
            'slug' => 'little-seeds-preschool',
            'timezone' => 'Asia/Manila',
        ]);

        $class = SchoolClass::factory()->create([
            'school_id' => $school->id,
            'name' => 'Sampaguita',
        ]);

        $teacher = StaffMember::factory()->create([
            'school_id' => $school->id,
            'name' => 'Teacher Ana Cruz',
        ]);

        $class->staffMembers()->attach($teacher, ['assignment_role' => 'lead']);

        $child = Child::factory()->create([
            'school_id' => $school->id,
            'school_class_id' => $class->id,
            'first_name' => 'Maya',
            'last_name' => 'Dela Cruz',
            'preferred_name' => 'Maya',
        ]);

        return [$school, $class, $child];
    }
}
