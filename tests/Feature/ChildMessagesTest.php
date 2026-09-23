<?php

namespace Tests\Feature;

use App\Models\Child;
use App\Models\ChildMessage;
use App\Models\Guardian;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\StaffMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChildMessagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_linked_parent_can_read_and_send_child_message(): void
    {
        [$school, , $child, $guardian, $teacherUser] = $this->createClassroom();
        $parent = $this->userWithRole($school, ChildMessage::Guardian, $guardian, [
            'name' => 'Rose Dela Cruz',
            'email' => 'rose.delacruz@sibol.test',
        ]);

        ChildMessage::factory()->create([
            'school_id' => $school->id,
            'child_id' => $child->id,
            'sender_id' => $teacherUser->id,
            'sender_role' => ChildMessage::Teacher,
            'body' => 'Maya enjoyed art today.',
            'sent_at' => '2026-09-24 10:00:00',
        ]);

        $this->actingAs($parent)
            ->get(route('children.messages.index', $child))
            ->assertOk()
            ->assertSee('Maya enjoyed art today.')
            ->assertSee('Child message thread');

        $this->actingAs($parent)
            ->post(route('children.messages.store', $child), [
                'body' => 'Thank you, Teacher Ana.',
            ])->assertRedirect();

        $this->assertDatabaseHas('child_messages', [
            'school_id' => $school->id,
            'child_id' => $child->id,
            'sender_id' => $parent->id,
            'sender_role' => ChildMessage::Guardian,
            'body' => 'Thank you, Teacher Ana.',
        ]);
    }

    public function test_assigned_teacher_can_send_child_message(): void
    {
        [$school, , $child, , $teacherUser] = $this->createClassroom();

        $this->actingAs($teacherUser)
            ->post(route('children.messages.store', $child), [
                'body' => 'Maya had a calm nap after lunch.',
            ])->assertRedirect();

        $this->assertDatabaseHas('child_messages', [
            'child_id' => $child->id,
            'sender_id' => $teacherUser->id,
            'sender_role' => ChildMessage::Teacher,
            'body' => 'Maya had a calm nap after lunch.',
        ]);
    }

    public function test_teacher_cannot_open_unassigned_child_thread(): void
    {
        [$school, , , , $teacherUser] = $this->createClassroom();
        $otherClass = SchoolClass::factory()->create(['school_id' => $school->id, 'name' => 'Mango']);
        $otherChild = Child::factory()->create([
            'school_id' => $school->id,
            'school_class_id' => $otherClass->id,
            'preferred_name' => 'Lia',
        ]);

        $this->actingAs($teacherUser)
            ->get(route('children.messages.index', $otherChild))
            ->assertForbidden();
    }

    public function test_parent_cannot_open_unlinked_child_thread(): void
    {
        [$school, $class, , $guardian] = $this->createClassroom();
        $parent = $this->userWithRole($school, ChildMessage::Guardian, $guardian);
        $otherChild = Child::factory()->create([
            'school_id' => $school->id,
            'school_class_id' => $class->id,
            'preferred_name' => 'Nico',
        ]);

        $this->actingAs($parent)
            ->get(route('children.messages.index', $otherChild))
            ->assertForbidden();
    }

    public function test_guest_is_redirected_from_child_messages(): void
    {
        [$school, $class] = $this->createClassroom();
        $child = Child::factory()->create([
            'school_id' => $school->id,
            'school_class_id' => $class->id,
        ]);

        $this->get(route('children.messages.index', $child))
            ->assertRedirect(route('login', absolute: false));
    }

    /**
     * @return array{0: School, 1: SchoolClass, 2: Child, 3: Guardian, 4: User}
     */
    private function createClassroom(): array
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
            'role' => 'teacher',
        ]);
        $class->staffMembers()->attach($staff, ['assignment_role' => 'lead']);
        $teacherUser = $this->userWithRole($school, ChildMessage::Teacher, $staff, [
            'name' => 'Teacher Ana Cruz',
            'email' => 'teacher.ana@sibol.test',
        ]);

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

    /**
     * @param  array{name?: string, email?: string}  $attributes
     */
    private function userWithRole(School $school, string $role, mixed $source, array $attributes = []): User
    {
        $user = User::factory()->create([
            'name' => $attributes['name'] ?? 'Demo User',
            'email' => $attributes['email'] ?? fake()->safeEmail(),
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
