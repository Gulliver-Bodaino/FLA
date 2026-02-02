<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

use Illuminate\Mail\Mailables\Address;

class FormBReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    private $params;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($params, $viewData)
    {
        $this->params = $params;
        $this->viewData = $viewData;

        // メール送信サーバの切り替え
        $mail = $params['mail'];
        config([
            'mail.mailers.smtp.host'     => $mail->mailers_smtp_host,
            'mail.mailers.smtp.port'     => (int) $mail->mailers_smtp_port,
            'mail.mailers.smtp.username' => $mail->mailers_smtp_username,
            'mail.mailers.smtp.password' => $mail->mailers_smtp_password,
//            'mail.from.address'          => $mail->from_address,
//            'mail.from.name'             => $mail->from_name,
        ]);

    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: $this->params['subject'],
            from: new Address($this->params['from'], $this->params['sender_name']),
            to: $this->params['to'],
            cc: $this->params['cc'],
            bcc: $this->params['bcc'],
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            text: 'emails.form_b',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
