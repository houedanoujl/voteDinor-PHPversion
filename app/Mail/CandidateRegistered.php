<?php

namespace App\Mail;

use App\Models\Candidate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CandidateRegistered extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Candidate $candidate
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nouvelle inscription candidat - Concours Photo DINOR',
            to: [config('mail.admin_email', 'admin@dinor.ci')]
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.candidate-registered',
            with: [
                'candidate' => $this->candidate,
                'approveUrl' => route('admin.filament.resources.candidates.edit', $this->candidate),
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}