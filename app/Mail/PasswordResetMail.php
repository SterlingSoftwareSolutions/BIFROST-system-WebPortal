<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use PragmaRX\Google2FA\Google2FA;
use App\Models\User;



class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public $google2fa_url;

    public function __construct(User $user)
    {
        $google2fa = new Google2FA();

        // Generate a 2FA secret if it's not already set
        if (!$user->google2fa_secret) {
            $secret = $google2fa->generateSecretKey();
            $user->google2fa_secret = $secret;
            $user->save();
        }

        // Generate a QR code URL for Google Authenticator
        $this->google2fa_url = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $user->google2fa_secret
        );
    }

    public function build()
    {
        return $this->subject('Password Reset Mail with 2FA')
                    ->view('emails.password-reset')
                    ->with('google2fa_url', $this->google2fa_url);
    }
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Password Reset Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'view.name',
        );
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
