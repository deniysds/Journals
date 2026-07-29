<?php

namespace Modules\Journals\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mattiverse\Userstamps\Traits\Userstamps;

/**
 * Class Journal
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $short_name
 * @property string|null $issn_p
 * @property string|null $issn_e
 * @property string|null $description
 * @property string|null $scope
 * @property string|null $guidelines
 * @property string|null $publication_ethics
 * @property bool $is_active
 *
 * @package Modules\Journals\Models
 */
class Journal extends Model
{
    use HasFactory, SoftDeletes, Userstamps;

    protected $table = 'journals';

    protected $fillable = [
        'name',
        'slug',
        'short_name',
        'issn_p',
        'issn_e',
        'description',
        'scope',
        'guidelines',
        'publication_ethics',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relationship to Journal Editorial Boards.
     */
    public function editorialBoards(): HasMany
    {
        return $this->hasMany(JournalEditorialBoard::class, 'journal_id')->orderBy('order_no', 'asc');
    }

    /**
     * Check if journal has active relations that prevent deletion.
     */
    public function hasActiveRelations(): bool
    {
        return $this->editorialBoards()->exists();
    }
}
