<?php

namespace Database\Factories;

use App\Models\Child;
use App\Models\ChildMessage;
use App\Models\School;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ChildMessage>
 */
class ChildMessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $school = School::factory();

        return [
            'school_id' => $school,
            'child_id' => Child::factory()->for($school),
            'sender_id' => User::factory(),
            'sender_role' => fake()->randomElement([ChildMessage::Teacher, ChildMessage::Guardian]),
            'body' => fake()->sentence(),
            'sent_at' => now('Asia/Manila'),
        ];
    }
}
