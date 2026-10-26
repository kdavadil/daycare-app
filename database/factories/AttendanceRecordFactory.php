<?php

namespace Database\Factories;

use App\Models\AttendanceRecord;
use App\Models\Child;
use App\Models\School;
use App\Models\SchoolClass;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AttendanceRecord>
 */
class AttendanceRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'school_id' => School::factory(),
            'school_class_id' => SchoolClass::factory(),
            'child_id' => Child::factory(),
            'type' => fake()->randomElement([AttendanceRecord::CheckIn, AttendanceRecord::CheckOut]),
            'occurred_at' => now('Asia/Manila'),
            'actor_name' => fake()->name(),
            'note' => null,
        ];
    }
}
