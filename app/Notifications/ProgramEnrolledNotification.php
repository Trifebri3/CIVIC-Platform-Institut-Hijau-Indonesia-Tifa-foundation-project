<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Program;

class ProgramEnrolledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $program;

    public function __construct(Program $program)
    {
        $this->program = $program;
    }

    public function via(object $notifiable): array
    {
        // Masuk ke Tabel Database (Dashboard) dan Email
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
        ->subject('🚀 Undangan Program: ' . $this->program->name)
        ->view('emails.program-invitation', [
            'user' => $notifiable,
            'program' => $this->program
        ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'program_name' => $this->program->name,
            'message' => 'Anda telah di-enroll ke program ' . $this->program->name,
            'url' => url('/dashboard/programs/' . $this->program->id),
        ];
    }
}
