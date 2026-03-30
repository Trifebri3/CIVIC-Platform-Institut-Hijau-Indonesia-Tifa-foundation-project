<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPassword extends ResetPassword
{
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('🔑 Atur Ulang Kata Sandi CIVIC')
            ->greeting('Halo!')
            ->line('Kami menerima permintaan atur ulang kata sandi untuk akun Anda.')
            ->action('Reset Password', url(config('app.url').route('password.reset', $this->token, false)))
            ->line('Tautan pemulihan ini akan kedaluwarsa dalam 60 menit.')
            ->line('Jika Anda tidak meminta ini, tidak ada tindakan lebih lanjut yang diperlukan.')
            ->salutation('Keamanan Akun, \n CIVIC Support System');
    }
}
