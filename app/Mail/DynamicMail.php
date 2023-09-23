<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;

class DynamicMail extends Mailable
{
    use Queueable, SerializesModels;

    public $title, $content;

    public function __construct($title, $content)
    {
        $this->title = $title;
        $this->content = $content;
    }

    public function build()
    {
		return $this->from(Config::get('mail.from.address'), Config::get('mail.from.name'))
		->subject($this->title)
        ->markdown('emails.dynamic-mail');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope( subject: 'Dynamic Mail', );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content( view: 'view.name', );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
