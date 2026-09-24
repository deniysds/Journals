<?php

namespace Modules\Journals\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Usermanagement\Models\User;

class JournalAnnouncement extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'journal_announcements';

    protected $fillable = [
        'journal_id',
        'title',
        'slug',
        'type',
        'summary',
        'content',
        'deadline',
        'banner_image',
        'attachment_file',
        'is_pinned',
        'is_published',
        'published_at',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'deadline'     => 'date',
            'published_at' => 'datetime',
            'is_pinned'    => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->slug) && !empty($model->title)) {
                $baseSlug = \Illuminate\Support\Str::slug($model->title);
                $slug = $baseSlug;
                $counter = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $counter++;
                }
                $model->slug = $slug;
            }
        });
    }

    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class, 'journal_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopePinned(Builder $query): Builder
    {
        return $query->where('is_pinned', true);
    }

    public function scopeCfp(Builder $query): Builder
    {
        return $query->where('type', 'call_for_papers');
    }

    public function isCfp(): bool
    {
        return $this->type === 'call_for_papers';
    }

    public function isDeadlinePassed(): bool
    {
        return $this->deadline && $this->deadline->isPast();
    }
}
