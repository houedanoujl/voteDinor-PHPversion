<?php

namespace App\Mail;

use App\Models\Candidate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CandidateApproved extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Candidate $candidate
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Votre candidature a été approuvée - Concours Photo DINOR',
            to: [$this->candidate->email ?: $this->candidate->whatsapp . '@example.com']
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.candidate-approved',
            with: [
                'candidate' => $this->candidate,
                'contestUrl' => route('home'),
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}