<?php

namespace App\Notifications;

use App\Models\Article;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PostRejected extends Notification
{
    use Queueable;

    /**
     * @param  string|null  $note  Reason given by the admin.
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
            'type' => 'post_rejected',
            'article_id' => $this->article->id,
            'title' => $this->article->title,
            'slug' => $this->article->slug,
            'admin_name' => $this->admin->name,
            'note' => $this->note,
            'message' => "\"{$this->article->title}\" was sent back for changes.",
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject("Changes requested: {$this->article->title}")
            ->greeting("Hello {$notifiable->name},")
            ->line("An admin sent \"{$this->article->title}\" back for changes.");

        if (filled($this->note)) {
            $mail->line('Reason: ' . $this->note);
        }

        return $mail->action('Edit post', url('/dashboard/posts/' . $this->article->id . '/edit'));
    }

    /**
     * Icons/colors for the bell UI.
     *
     * @return array{icon: string, color: string}
     */
    public static function presentation(): array
    {
        return ['icon' => 'fa-solid fa-circle-xmark', 'color' => 'text-danger'];
    }
}