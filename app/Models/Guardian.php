<?php

namespace App\Models;

use Database\Factories\GuardianFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['school_id', 'first_name', 'last_name', 'email', 'phone'])]
class Guardian extends Model
{
    /** @use HasFactory<GuardianFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<School, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * @return BelongsToMany<Child, $this>
     */
    public function children(): BelongsToMany
    {
        return $this->belongsToMany(Child::class)
            ->withPivot(['relationship', 'is_primary', 'can_pick_up'])
            ->withTimestamps();
    }
}
