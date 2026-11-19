<?php

namespace App\Models;

use App\Models\Concerns\Followable;
use Illuminate\Database\Eloquent\Model;

// ⚠️ تأكد من هذه الـ imports
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Auth\User as AuthenticatableUser; // هذا مهم
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Image\Enums\Fit;

class User extends AuthenticatableUser implements Authenticatable, HasMedia
{
    use HasFactory, Notifiable, HasRoles, InteractsWithMedia;


    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',

        'locale',
        'notifications_last_read_at',
        'reputation',

        'provider',
        'provider_id',
        'provider_avatar',

        'email_verified_at'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean'
    ];

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function articleTranslations(): HasMany
    {
        return $this->hasMany(ArticleTranslation::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(ArticleCategory::class, 'article_category_id');
    }

    // Comments
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
    public function commentVotes()
    {
        return $this->hasMany(CommentVote::class);
    }


    public function reacitons(): HasMany
    {
        return $this->hasMany(Reaction::class);
    }

    public function favoriteCategories()
    {
        return $this->belongsToMany(ArticleCategory::class, 'article_category_user')->withTimestamps();
    }

    /*
    public function savedArticles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'article_user')->withTimestamps();
    }
    */

    // To get the formatted role name of the user
    public function formattedRole(): Attribute
    {
        return Attribute::make(
            get: function () {
                $role = $this->roles->first();
                return $role ? ucwords(str_replace('-', ' ', $role->name)) : 'User';
            }
        );
    }

    public function mentionedInComments(): BelongsToMany
    {
        // نحدد اسم الجدول الوسيط وعمود الـ foreign keys بدقة
        return $this->belongsToMany(
            Comment::class,            // النموذج المرتبط (التعليقات)
            'comment_user_mentions',   // اسم الجدول الوسيط
            'user_id',                 // المفتاح الأجنبي الخاص بالمستخدم في الجدول الوسيط
            'comment_id'               // المفتاح الأجنبي الخاص بالتعليق في الجدول الوسيط
        )->withTimestamps();
    }

    public function suggestions(): HasMany
    {
        return $this->hasMany(Suggestion::class, 'user_id');
    }

    /*
    public function votedSuggestions(): BelongsToMany
    {
        return $this->belongsToMany(Suggestion::class, 'suggestion_votes', 'user_id', 'suggestion_id')->withTimestamps();
    }
    */

    public function votedSuggestions()
    {
        return $this->belongsToMany(Suggestion::class, 'suggestion_votes', 'user_id', 'suggestion_id')->withTimestamps();
    }


    // داخل موديل User

    public function penalties()
    {
        return $this->hasMany(Penalty::class);
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function activePenalty()
    {
        return $this->hasOne(Penalty::class)
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at') // حظر دائم (الدرجة 5)
                    ->orWhere('expires_at', '>', now()); // حظر مؤقت لم ينتهِ بعد
            })
            ->latest();
    }

    /**
     * فحص هل المستخدم محظور حالياً من النشر/التعليق
     */
    public function isBanned(): bool
    {
        return $this->activePenalty()->exists();
    }


    // For Reputation points
    public function addReputation(int $points)
    {
        $this->increment('reputation', $points);
    }

    public function deductReputation(int $points)
    {
        $this->decrement('reputation', max(0, $this->reputation - $points));
    }

    public function mediaSummeries(): HasMany
    {
        return $this->hasMany(MediaSummary::class, 'user_id');
    }

    public function mediaSummeryTranslations(): HasMany
    {
        return $this->hasMany(MediaSummaryTranslation::class, 'user_id');
    }

    /**
     * الكتاب الذين يتابعهم هذا المستخدم
     */
    /*
    public function followingAuthors()
    {
        return $this->belongsToMany(User::class, 'author_followers', 'user_id', 'author_id');
    }
    */

    /**
     * المتابعون الذين يتابعون هذا المستخدم (إذا كان كاتباً)
     */
    /*
    public function authorFollowers()
    {
        return $this->belongsToMany(User::class, 'author_followers', 'author_id', 'user_id');
    }


    public function followedAuthors()
    {
        return $this->morphedByMany(User::class, 'followable', 'follows');
    }
    public function followedArticleCategories()
    {
        return $this->morphedByMany(ArticleCategory::class, 'followable', 'follows');
    }
    public function followedArticleTags()
    {
        return $this->morphedByMany(ArticleTag::class, 'followable', 'follows');
    }
    */


    /**
     * الفئات التي يتابعها المستخدم
     */
    public function followedArticleCategories(): MorphToMany
    {
        return $this->morphedByMany(
            ArticleCategory::class, // الموديل المستهدف
            'followable',           // اسم العلاقة (نفس اسم morphs)
            'follows',              // اسم جدول الربط
            'user_id',              // مفتاح المستخدم في الجدول الوسيط
            'followable_id',        // مفتاح الكيان المتابع
            'id',                   // مفتاح المستخدم الأساسي
            'id'                    // مفتاح الكيان الأساسي
        )->withTimestamps();
    }

    /**
     * الوسوم التي يتابعها المستخدم
     */
    public function followedArticleTags(): MorphToMany
    {
        return $this->morphedByMany(
            ArticleTag::class,
            'followable',
            'follows',
            'user_id',
            'followable_id',
            'id',
            'id'
        )->withTimestamps();
    }

    public function followedBookCategories(): MorphToMany
    {
        return $this->morphedByMany(
            BookCategory::class,
            'followable',
            'follows',
            'user_id',
            'followable_id',
            'id',
            'id'
        )->withTimestamps();
    }

    public function followedBookTags(): MorphToMany
    {
        return $this->morphedByMany(
            BookTag::class,
            'followable',
            'follows',
            'user_id',
            'followable_id',
            'id',
            'id'
        )->withTimestamps();
    }

    public function followedGameCategories(): MorphToMany
    {
        return $this->morphedByMany(
            GameCategory::class,
            'followable',
            'follows',
            'user_id',
            'followable_id',
            'id',
            'id'
        )->withTimestamps();
    }

    public function followedGameTags(): MorphToMany
    {
        return $this->morphedByMany(
            GameTag::class,
            'followable',
            'follows',
            'user_id',
            'followable_id',
            'id',
            'id'
        )->withTimestamps();
    }

    public function followedMediaSummaryCategories(): MorphToMany
    {
        return $this->morphedByMany(
            MediaSummaryCategory::class,
            'followable',
            'follows',
            'user_id',
            'followable_id',
            'id',
            'id'
        )->withTimestamps();
    }

    public function followedMediaSummaryTags(): MorphToMany
    {
        return $this->morphedByMany(
            MediaSummaryTag::class,
            'followable',
            'follows',
            'user_id',
            'followable_id',
            'id',
            'id'
        )->withTimestamps();
    }



    /**
     * جميع المتابعات (اختياري) - إذا أردت جلب كل شيء
     */
    public function follows()
    {
        return $this->hasMany(Follow::class);
    }

    public function savedArticles(): MorphToMany
    {
        return $this->morphedByMany(Article::class, 'savable', 'saved_items', 'user_id', 'savable_id', 'id', 'id')
            ->withTimestamps();
    }

    public function savedBooks(): MorphToMany
    {
        return $this->morphedByMany(Book::class, 'savable', 'saved_items', 'user_id', 'savable_id', 'id', 'id')
            ->withTimestamps();
    }

    public function savedGames(): MorphToMany
    {
        return $this->morphedByMany(Game::class, 'savable', 'saved_items', 'user_id', 'savable_id', 'id', 'id')
            ->withTimestamps();
    }

    public function savedMediaSummaries(): MorphToMany
    {
        return $this->morphedByMany(MediaSummary::class, 'savable', 'saved_items', 'user_id', 'savable_id', 'id', 'id')
            ->withTimestamps();
    }


    public function authorFollowers(): MorphToMany
    {
        return $this->morphToMany(
            User::class,
            'followable',
            'follows',
            'followable_id',
            'user_id',
            'id',
            'id'
        )->withTimestamps();
    }

    public function followingAuthors(): MorphToMany
    {
        return $this->morphedByMany(
            User::class,
            'followable',
            'follows',
            'user_id',
            'followable_id',
            'id',
            'id'
        )->withTimestamps();
    }


    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar_imgs')
            ->singleFile(); // لضمان احتفاظ المستخدم بصورة شخصية واحدة فقط
    }


    public function registerMediaConversions(?Media $media = null): void
    {
        // حجم مصغر ومربع للتعليقات والقوائم الصغيرة (مثلاً 150x150 بكسل)
        $this->addMediaConversion('thumb')
            /*
            ->square() // يضمن أن تكون الصورة مربعة
            ->width(150)
            ->height(150)
            */
            ->fit(Fit::Crop, 150, 150)
            ->sharpen(10) // تحسين حِدّة الصورة
            ->nonQueued(); // التنفيذ بشكل فوري (أو يمكنك تركها للـ Queue إذا كان لديك ضغط كبير)

        // حجم متوسط لصفحة البروفايل (مثلاً 300x300 بكسل)
        $this->addMediaConversion('medium')
            /*
            ->square()
            ->width(300)
            ->height(300)
            */
            ->fit(Fit::Crop, 300, 300)
            ->nonQueued();
    }
}
