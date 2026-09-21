<?php

namespace Database\Factories;

use App\Models\School;
use App\Models\StaffMember;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StaffMember>
 */
class StaffMemberFactory extends Factory
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
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'role' => fake()->randomElement(['teacher', 'administrator']),
            'status' => 'active',
        ];
    }
}
