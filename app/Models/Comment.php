<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'article_id',
        'parent_id',
        'content',
        'status',
        'likes',
    ];

    protected function casts(): array
    {
        return [
            'likes' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * The comment this one is replying to (null for top level comments).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Direct replies to this comment.
     */
    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->where('status', 'approved');
    }

    /**
     * Users who liked this comment.
     */
    public function likers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'comment_likes');
    }

    /**
     * How deep this comment sits in the thread (0 for top level comments).
     */
    public function depth(): int
    {
        $depth = 0;
        $parentId = $this->parent_id;

        // Guard against cycles introduced by manual data edits.
        $visited = [$this->getKey() => true];

        while ($parentId !== null && $depth < 50) {
            if (isset($visited[$parentId])) {
                break;
            }

            $visited[$parentId] = true;
            $depth++;
            $parentId = static::whereKey($parentId)->value('parent_id');
        }

        return $depth;
    }

    /**
     * Whether the given user has liked this comment.
     */
    public function isLikedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        return $this->relationLoaded('likers')
            ? $this->likers->contains('id', $user->id)
            : $this->likers()->where('users.id', $user->id)->exists();
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Keys of this comment and every descendant reply, deepest last.
     *
     * Replies can nest arbitrarily deep, so walk level by level instead of
     * trusting a fixed depth limit.
     *
     * @return array<int, int>
     */
    public function descendantIds(): array
    {
        $ids = [];
        $level = static::where('parent_id', $this->getKey())->pluck('id')->all();

        while ($level !== []) {
            $ids = array_merge($ids, $level);
            $level = static::whereIn('parent_id', $level)->pluck('id')->all();
        }

        return $ids;
    }
}
