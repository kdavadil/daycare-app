<?php

namespace Database\Factories;

use App\Models\Child;
use App\Models\JournalEntry;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JournalEntry>
 */
class JournalEntryFactory extends Factory
{
    protected $model = JournalEntry::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $school = School::factory()->create();
        $class = SchoolClass::factory()->create(['school_id' => $school->id]);
        $child = Child::factory()->create([
            'school_id' => $school->id,
            'school_class_id' => $class->id,
        ]);

        return [
            'school_id' => $school->id,
            'school_class_id' => $class->id,
            'child_id' => $child->id,
            'author_id' => User::factory(),
            'category' => JournalEntry::LearningMoment,
            'title' => fake()->sentence(4),
            'body' => fake()->paragraph(),
            'meal_amount' => null,
            'photo_path' => null,
            'photo_original_name' => null,
            'occurred_at' => now('Asia/Manila'),
            'status' => 'published',
        ];
    }
}
