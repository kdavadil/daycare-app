<?php

namespace App\Models;

use Database\Factories\SchoolFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'address', 'timezone'])]
class School extends Model
{
    /** @use HasFactory<SchoolFactory> */
    use HasFactory;

    /**
     * @return HasMany<SchoolClass, $this>
     */
    public function classes(): HasMany
    {
        return $this->hasMany(SchoolClass::class);
    }

    /**
     * @return HasMany<Child, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(Child::class);
    }

    /**
     * @return HasMany<Guardian, $this>
     */
    public function guardians(): HasMany
    {
        return $this->hasMany(Guardian::class);
    }

    /**
     * @return HasMany<StaffMember, $this>
     */
    public function staffMembers(): HasMany
    {
        return $this->hasMany(StaffMember::class);
    }

    /**
     * @return HasMany<ClassCalendarEvent, $this>
     */
    public function classCalendarEvents(): HasMany
    {
        return $this->hasMany(ClassCalendarEvent::class);
    }

    /**
     * @return HasMany<ChildMessage, $this>
     */
    public function childMessages(): HasMany
    {
        return $this->hasMany(ChildMessage::class);
    }

    /**
     * @return HasMany<SchoolUserMembership, $this>
     */
    public function userMemberships(): HasMany
    {
        return $this->hasMany(SchoolUserMembership::class);
    }
}
