<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AnnouncementNotification extends Notification
{
    use Queueable;

    public $announcement;

    /**
     * Kita masukkan data pengumuman ke dalam constructor
     * agar bisa dipanggil di email dan database.
     */
    public function __construct($announcement)
    {
        $this->announcement = $announcement;
    }

    /**
     * Tentukan channel pengiriman: Mail (Email) & Database (Lonceng Web).
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Format tampilan Email.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('📢 PENGUMUMAN: ' . $this->announcement->title)
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Ada informasi penting baru untuk Anda:')
            ->line('"' . $this->announcement->message . '"')
            ->action('Lihat Detail Pengumuman', url('/dashboard'))
            ->line('Terima kasih telah menjadi bagian dari Civic Platform.');
    }

    /**
     * Data yang disimpan ke database (untuk icon lonceng).
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->announcement->title,
            'message' => $this->announcement->message,
            'link' => $this->announcement->link_url ?? '/dashboard',
            'type' => $this->announcement->type,
        ];
    }
}
