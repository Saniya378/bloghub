<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'is_active',
        'avatar',
        'cover_image',
        'bio',
        'tagline',
        'specialty',
        'social',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'social' => 'array',
        ];
    }

    /**
     * Check if the user account is active.
     */
    public function isActive(): bool
    {
        return (bool) ($this->is_active ?? true);
    }

    /**
     * Check if the user has a specific role.
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Get the dashboard URL for the user based on their role.
     */
    public function dashboardUrl(): string
    {
        return match ($this->role) {
            'admin' => '/admin',
            'author' => '/dashboard',
            default => '/',
        };
    }

    /**
     * Articles written by the user.
     */
    public function articles(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\Article::class);
    }

    /**
     * Comments written by the user.
     */
    public function comments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\Comment::class);
    }

    /**
     * Articles liked by the user.
     */
    public function articleLikes(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Article::class, 'article_likes');
    }

    /**
     * Articles bookmarked by the user.
     */
    public function bookmarks(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Article::class, 'bookmarks');
    }

    /**
     * Authors followed by the user.
     */
    public function follows(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(\App\Models\User::class, 'follows', 'user_id', 'author_id');
    }

    /**
     * Users who follow this user.
     */
    public function followers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(\App\Models\User::class, 'follows', 'author_id', 'user_id');
    }

    /**
     * Alias for followers(): users this user is followed by.
     */
    public function following(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->followers();
    }

    /**
     * Comments liked by the user.
     */
    public function commentLikes(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Comment::class, 'comment_likes');
    }
}
