<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Program;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProgramInvitationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    // --- WAJIB ADA DUA BARIS INI ---
    public $user;
    public $program;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, Program $program)
    {
        // Masukkan data dari parameter ke property public di atas
        $this->user = $user;
        $this->program = $program;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🚀 Undangan Resmi Program: ' . $this->program->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            // Pastikan kamu sudah buat file view ini di resources/views/emails/
            view: 'emails.program-invitation',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
