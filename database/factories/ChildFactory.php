<?php

namespace Database\Factories;

use App\Models\Child;
use App\Models\School;
use App\Models\SchoolClass;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Child>
 */
class ChildFactory extends Factory
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
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'preferred_name' => fake()->firstName(),
            'birthdate' => fake()->dateTimeBetween('-6 years', '-2 years')->format('Y-m-d'),
            'status' => 'active',
        ];
    }
}
