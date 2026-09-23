<?php

namespace App\Models;

use Database\Factories\ChildMessageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['school_id', 'child_id', 'sender_id', 'sender_role', 'body', 'sent_at'])]
class ChildMessage extends Model
{
    /** @use HasFactory<ChildMessageFactory> */
    use HasFactory;

    public const Administrator = 'administrator';

    public const Teacher = 'teacher';

    public const Guardian = 'guardian';

    /**
     * @return BelongsTo<School, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
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
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function displayRole(): string
    {
        return match ($this->sender_role) {
            self::Administrator => 'Admin',
            self::Teacher => 'Teacher',
            self::Guardian => 'Parent',
            default => str($this->sender_role)->headline()->toString(),
        };
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
        ];
    }
}
