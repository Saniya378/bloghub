<?php

namespace App\Models;

use App\Notifications\PostApproved;
use App\Notifications\PostRejected;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Article extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PENDING = 'pending';

    public const STATUS_PUBLISHED = 'published';

    public const STATUS_REJECTED = 'rejected';

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
        'submitted_at',
        'reviewed_at',
        'reviewed_by',
        'review_note',
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'views' => 'integer',
            'likes' => 'integer',
            'comment_count' => 'integer',
            // datetime, not date: a "date" cast truncates the time component, which made
            // a post published at 10:05 read as "10 hrs ago" on the blog cards.
            'published_at' => 'datetime',
            'submitted_at' => 'datetime',
            'reviewed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The admin who reviewed this article, if any.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /**
     * Waiting for an admin decision.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Sent back by an admin, the author can edit and resubmit.
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Human readable status for badges.
     */
    public function statusLabel(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'In Review',
            self::STATUS_REJECTED => 'Rejected',
            default => ucfirst((string) $this->status),
        };
    }

    /**
     * Bootstrap badge class for the status.
     */
    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_PUBLISHED => 'bg-success',
            self::STATUS_PENDING => 'bg-info text-dark',
            self::STATUS_REJECTED => 'bg-danger',
            default => 'bg-warning text-dark',
        };
    }

    /**
     * Send the article to the admin review queue.
     */
    public function submitForReview(): void
    {
        $this->forceFill([
            'status' => self::STATUS_PENDING,
            'submitted_at' => now(),
            'reviewed_at' => null,
            'reviewed_by' => null,
            'review_note' => null,
        ])->save();

        ActivityLog::log('Submitted', 'Article', "Author \"{$this->user?->name}\" submitted article \"{$this->title}\" for review", $this, $this->user);
    }

    /**
     * Publish the article after an admin approval.
     */
    public function approve(User $admin, ?string $note = null): void
    {
        $this->forceFill([
            'status' => self::STATUS_PUBLISHED,
            'published_at' => $this->published_at ?? now(),
            'reviewed_at' => now(),
            'reviewed_by' => $admin->id,
            'review_note' => $note,
        ])->save();

        ActivityLog::log('Approved', 'Article', "Admin approved article \"{$this->title}\"", $this, $admin);

        $this->user?->notify(new PostApproved($this, $admin, $note));
    }

    /**
     * Send the article back to the author with a reason.
     */
    public function reject(User $admin, ?string $note = null): void
    {
        $this->forceFill([
            'status' => self::STATUS_REJECTED,
            'reviewed_at' => now(),
            'reviewed_by' => $admin->id,
            'review_note' => $note,
        ])->save();

        ActivityLog::log('Rejected', 'Article', "Admin rejected article \"{$this->title}\"", $this, $admin);

        $this->user?->notify(new PostRejected($this, $admin, $note));
    }

    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
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
