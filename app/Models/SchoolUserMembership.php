<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'school_id', 'role', 'status', 'source_type', 'source_id'])]
class SchoolUserMembership extends Model
{
    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<School, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function displayRole(): string
    {
        return match ($this->role) {
            'administrator' => 'School administrator',
            'teacher' => 'Teacher',
            'guardian' => 'Parent or guardian',
            default => str($this->role)->headline()->toString(),
        };
    }
}
