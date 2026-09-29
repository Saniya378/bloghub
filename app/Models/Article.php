<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'category_slug',
        'tags',
        'image',
        'status',
        'views',
        'likes',
        'comment_count',
        'reading_time',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'views' => 'integer',
            'likes' => 'integer',
            'comment_count' => 'integer',
            'published_at' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeVisibleTo($query, ?User $user)
    {
        if (!$user) {
            return $query->published();
        }

        if ($user->hasRole('admin')) {
            return $query;
        }

        return $query->where(function ($q) use ($user) {
            $q->published()->orWhere('user_id', $user->id);
        });
    }
}
