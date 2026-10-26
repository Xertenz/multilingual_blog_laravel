<?php

namespace App\Models;

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
use Spatie\Permission\Traits\HasRoles;

class User extends AuthenticatableUser implements Authenticatable
{
    use HasFactory, Notifiable, HasRoles;


    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'locale',
        'notifications_last_read_at',
        'reputation'
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

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function reacitons(): HasMany
    {
        return $this->hasMany(Reaction::class);
    }

    public function favoriteCategories()
    {
        return $this->belongsToMany(Category::class, 'category_user')->withTimestamps();
    }

    public function savedArticles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'article_user')->withTimestamps();
    }

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
}
