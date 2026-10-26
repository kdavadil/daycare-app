<?php

namespace Database\Factories;

use App\Models\School;
use App\Models\SchoolClass;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SchoolClass>
 */
class SchoolClassFactory extends Factory
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
            'name' => fake()->randomElement(['Sampaguita', 'Mango', 'Acacia', 'Narra']),
            'age_group' => fake()->randomElement(['Toddler', 'Nursery', 'Kinder 1', 'Kinder 2']),
            'room' => fake()->randomElement(['Room 1', 'Room 2', 'Garden Room']),
        ];
    }
}
