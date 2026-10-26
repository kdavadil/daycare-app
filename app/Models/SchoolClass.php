<?php

namespace App\Models;

use Database\Factories\SchoolClassFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['school_id', 'name', 'age_group', 'room'])]
class SchoolClass extends Model
{
    /** @use HasFactory<SchoolClassFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<School, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * @return HasMany<Child, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(Child::class);
    }

    /**
     * @return HasMany<ClassCalendarEvent, $this>
     */
    public function calendarEvents(): HasMany
    {
        return $this->hasMany(ClassCalendarEvent::class);
    }

    /**
     * @return BelongsToMany<StaffMember, $this>
     */
    public function staffMembers(): BelongsToMany
    {
        return $this->belongsToMany(StaffMember::class)
            ->withPivot(['assignment_role'])
            ->withTimestamps();
    }
}
