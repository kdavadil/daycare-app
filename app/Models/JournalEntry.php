<?php

namespace App\Models;

use Database\Factories\JournalEntryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['school_id', 'school_class_id', 'child_id', 'author_id', 'category', 'title', 'body', 'meal_amount', 'photo_path', 'photo_original_name', 'occurred_at', 'status'])]
class JournalEntry extends Model
{
    /** @use HasFactory<JournalEntryFactory> */
    use HasFactory;

    public const string LearningMoment = 'learning_moment';

    public const string Activity = 'activity';

    public const string Meal = 'meal';

    public const string Rest = 'rest';

    public const string Care = 'care';

    public const string Mood = 'mood';

    /**
     * @return array<string, string>
     */
    public static function categories(): array
    {
        return [
            self::LearningMoment => 'Learning moment',
            self::Activity => 'Activity',
            self::Meal => 'Meal',
            self::Rest => 'Rest / nap',
            self::Care => 'Care note',
            self::Mood => 'Mood',
        ];
    }

    public function categoryLabel(): string
    {
        return self::categories()[$this->category] ?? str($this->category)->headline()->toString();
    }

    /**
     * @return BelongsTo<School, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * @return BelongsTo<SchoolClass, $this>
     */
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    /**
     * @return BelongsTo<Child, $this>
     */
    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'occurred_at' => 'datetime',
        ];
    }
}
