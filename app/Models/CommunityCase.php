<?php

namespace App\Models;

use App\Enums\CommunityCaseStatus;
use App\Enums\ModerationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CommunityCase extends Model
{
    use HasFactory;

    protected $guarded = [];

    // إضافة الـ Casts هنا
    protected $casts = [
        'moderation_status' => ModerationStatus::class,
        'case_status' => CommunityCaseStatus::class,
    ];

    /**
     * Scope لجلب القضايا المقبولة فقط وحسب اللغة الحالية
     */
    public function scopeApprovedForCurrentLocale(Builder $query): Builder
    {
        return $query->where('moderation_status', 'approved')
            ->where('locale', app()->getLocale());
    }

    /**
     * العلاقة مع العضو صاحب القضية
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /*
    public function comments()
    {
        return $this->hasMany(CaseComment::class)->whereNull('parent_id')->latest();
    }
    */

    public function category()
    {
        return $this->belongsTo(CommunityCaseCategory::class, 'community_case_category_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(
            CommunityCaseTag::class,
            'community_case_tag',
            'community_case_id',
            'community_case_tag_id'
        )->withTimestamps();
    }

    public function activeTags()
    {
        return $this->tags()->where('community_case_tags.is_active', true);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function reports(): MorphMany
    {
        return $this->morphMany(Report::class, 'reportable');
    }
}
