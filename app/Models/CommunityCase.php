<?php

namespace App\Models;

use App\Enums\CommunityCaseStatus;
use App\Enums\ModerationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Builder;

class CommunityCase extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'community_case_category_id',
        'locale',
        'title',
        'slug',
        'body',
        'moderation_status',
        'case_status',
        'rejection_reason',
        'views_count',
        'comments_count',
    ];

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
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
