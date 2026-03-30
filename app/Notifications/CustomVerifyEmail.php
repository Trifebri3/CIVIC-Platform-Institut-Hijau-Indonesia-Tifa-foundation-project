<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class CustomVerifyEmail extends VerifyEmail
{
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('🛡️ Verifikasi Akun CIVIC Platform')
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Terima kasih telah bergabung. Silakan klik tombol di bawah ini untuk memverifikasi alamat email Anda dan mengaktifkan akses penuh.')
            ->action('Verifikasi Email Saya', $verificationUrl)
            ->line('Jika Anda tidak merasa mendaftar, abaikan pesan ini.')
            ->salutation('Salam Perubahan, \n Tim CIVIC Platform');
    }
}
