<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;



class ProgramInvitationNotification extends Notification
{
    use Queueable;

    protected $participant;

    public function __construct($participant)
    {
        $this->participant = $participant;
    }

    // Aktifkan channel 'mail' dan 'database'
public function via($notifiable)
{
    // Tambahkan 'database' agar muncul di lonceng header
    return ['mail', 'database'];
}

public function toMail($notifiable)
{
    // Gunakan ?? untuk kasih nama cadangan kalau program-nya null
    $namaProgram = $this->participant->program->nama_program ?? 'Program Spesial';

    return (new MailMessage)
        ->subject('Undangan Program Khusus: ' . $namaProgram)
        ->greeting('Halo, ' . $notifiable->name . '!')
        ->line('Anda telah diundang untuk bergabung dalam program: ' . $namaProgram)
        ->action('Cek Dashboard', url('/dashboard'))
        ->line('Selamat bergabung!');
}

public function toArray($notifiable)
{
    // Data ini yang akan dibaca oleh header Bos
    return [
        'program_name' => $this->participant->program->nama_program,
        'message' => 'Anda telah ditambahkan ke program: ' . $this->participant->program->nama_program,
        'type' => 'whitelist_invitation',
        'url' => '/dashboard/programs', // Sesuaikan URL tujuan
    ];
}
}
