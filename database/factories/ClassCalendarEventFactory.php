<?php

namespace Database\Factories;

use App\Models\ClassCalendarEvent;
use App\Models\School;
use App\Models\SchoolClass;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ClassCalendarEvent>
 */
class ClassCalendarEventFactory extends Factory
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
            'school_class_id' => SchoolClass::factory()->for($school),
            'title' => fake()->randomElement(['Art day', 'Music circle', 'Garden walk', 'Family day practice']),
            'description' => fake()->sentence(),
            'event_type' => fake()->randomElement(array_keys(ClassCalendarEvent::types())),
            'event_date' => now('Asia/Manila')->toDateString(),
            'starts_at' => now('Asia/Manila')->setTime(9, 0),
            'ends_at' => now('Asia/Manila')->setTime(10, 0),
        ];
    }
}
