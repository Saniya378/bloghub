<?php

namespace App\Notifications;

use App\Models\Article;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PostApproved extends Notification
{
    use Queueable;

    /**
     * @param  string|null  $note  Optional note from the admin.
     */
    public function __construct(
        public Article $article,
        public User $admin,
        public ?string $note = null,
    ) {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'post_approved',
            'article_id' => $this->article->id,
            'title' => $this->article->title,
            'slug' => $this->article->slug,
            'admin_name' => $this->admin->name,
            'note' => $this->note,
            'message' => "\"{$this->article->title}\" has been approved and is now live.",
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Your post is live: {$this->article->title}")
            ->greeting("Good news, {$notifiable->name}!")
            ->line("An admin approved \"{$this->article->title}\" and it is now published.")
            ->action('View post', url('/blogs/' . $this->article->slug));
    }

    /**
     * Icons/colors for the bell UI.
     *
     * @return array{icon: string, color: string}
     */
    public static function presentation(): array
    {
        return ['icon' => 'fa-solid fa-circle-check', 'color' => 'text-success'];
    }
}