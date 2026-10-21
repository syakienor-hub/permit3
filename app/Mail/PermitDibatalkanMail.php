<?php

namespace App\Mail;

use App\Models\Permit;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PermitDibatalkanMail extends Mailable
{
    use Queueable, SerializesModels;

    public Permit $permit;

    public string $detailUrl;

    public function __construct(Permit $permit)
    {
        $this->permit = $permit->loadMissing(['user', 'classifications']);
        $this->detailUrl = url('/admin/approvals/' . $permit->id);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[Permit Dibatalkan] {$this->permit->no_permit} — {$this->permit->nama_pekerjaan}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.permit-dibatalkan',
            with: [
                'permit' => $this->permit,
                'detailUrl' => $this->detailUrl,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
