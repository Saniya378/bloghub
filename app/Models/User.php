<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

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
     * Guarantee a username exists, since author profile URLs depend on it.
     */
    protected static function booted(): void
    {
        $ensureUsername = function (self $user): void {
            if (blank($user->username)) {
                $user->username = static::generateUniqueUsername($user->name);
            }
        };

        static::creating($ensureUsername);
        static::updating($ensureUsername);
    }

    /**
     * Build a slug from the display name that is not taken yet.
     */
    public static function generateUniqueUsername(?string $name): string
    {
        $base = Str::slug($name ?? '');
        $base = $base === '' ? 'user' : Str::limit($base, 100, '');

        $username = $base;
        $suffix = 2;

        while (static::where('username', $username)->exists()) {
            $username = Str::limit($base, 100 - strlen((string) $suffix) - 1, '') . '-' . $suffix++;
        }

        return $username;
    }

    /**
     * The author profile URL, or null when the user has no username yet.
     */
    public function profileUrl(): ?string
    {
        return filled($this->username) ? route('authors.show', $this->username) : null;
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
        return $this->belongsToMany(\App\Models\Article::class, 'article_likes')->withTimestamps();
    }

    /**
     * Articles bookmarked by the user.
     */
    public function bookmarks(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Article::class, 'bookmarks')->withTimestamps();
    }

    /**
     * Authors followed by the user.
     */
    public function follows(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(\App\Models\User::class, 'follows', 'user_id', 'author_id')->withTimestamps();
    }

    /**
     * Users who follow this user.
     */
    public function followers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(\App\Models\User::class, 'follows', 'author_id', 'user_id')->withTimestamps();
    }

    /**
     * Alias for follows(): authors this user is following.
     */
    public function following(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->follows();
    }

    /**
     * Comments liked by the user.
     */
    public function commentLikes(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Comment::class, 'comment_likes')->withTimestamps();
    }
}
