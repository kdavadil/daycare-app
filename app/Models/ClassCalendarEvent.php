<?php

namespace App\Models;

use Database\Factories\ClassCalendarEventFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['school_id', 'school_class_id', 'title', 'description', 'event_type', 'event_date', 'starts_at', 'ends_at'])]
class ClassCalendarEvent extends Model
{
    /** @use HasFactory<ClassCalendarEventFactory> */
    use HasFactory;

    public const Activity = 'activity';

    public const Reminder = 'reminder';

    public const SchoolEvent = 'school_event';

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
     * @return array<string, string>
     */
    public static function types(): array
    {
        return [
            self::Activity => 'Class activity',
            self::Reminder => 'Reminder',
            self::SchoolEvent => 'School event',
        ];
    }

    public function displayType(): string
    {
        return self::types()[$this->event_type] ?? str($this->event_type)->headline()->toString();
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }
}
