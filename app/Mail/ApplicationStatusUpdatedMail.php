<?php

namespace App\Mail;

use App\Models\AssessmentApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicationStatusUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public AssessmentApplication $application
    ) {}

    public function envelope(): Envelope
    {
        $statusText = $this->application->status->label();

        return new Envelope(
            subject: 'Pembaruan Status Permohonan: '.$this->application->nomor_pendaftaran.' ['.$statusText.']',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.application_status_updated',
        );
    }
}
