<?php

namespace Modules\Journals\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mattiverse\Userstamps\Traits\Userstamps;
use Modules\Usermanagement\Models\User;

/**
 * Class JournalEditorialBoard
 *
 * @property int $id
 * @property int $journal_id
 * @property int|null $user_id
 * @property string|null $name
 * @property string|null $email
 * @property string|null $affiliation
 * @property string $role
 * @property int $order_no
 * @property bool $is_active
 *
 * @package Modules\Journals\Models
 */
class JournalEditorialBoard extends Model
{
    use HasFactory, SoftDeletes, Userstamps;

    protected $table = 'journal_editorial_boards';

    protected $fillable = [
        'journal_id',
        'user_id',
        'name',
        'email',
        'affiliation',
        'role',
        'order_no',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'order_no'  => 'integer',
        ];
    }

    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class, 'journal_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Accessor for display name (from registered user or manual name)
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->user?->name ?? $this->name ?? '-';
    }

    /**
     * Accessor for display email
     */
    public function getDisplayEmailAttribute(): string
    {
        return $this->user?->email ?? $this->email ?? '-';
    }
}
