<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class AdminPasswordResetMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $recipient,
        public string $password,
        public bool $forceChange = true,
        public ?int $sentByAdminId = null,
    ) {}

    public function envelope(): Envelope
    {
        $keys = Cache::get('settings:admin', []);

        $supportEmail = ($keys['general.supportEmail'] ?? null)
            ?: config('loops.support_email')
            ?: config('mail.from.address');

        $instanceName = ($keys['general.instanceName'] ?? null)
            ?: config('mail.from.name')
            ?: config('app.name');

        return new Envelope(
            subject: 'Your PornTk password has been reset',
            from: new Address($supportEmail, $instanceName),
            tags: ['admin-action', 'password-reset'],
            metadata: [
                'recipient_id' => (string) $this->recipient->id,
                'force_change' => $this->forceChange ? '1' : '0',
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.admin.password_reset',
            with: [
                'recipient' => $this->recipient,
                'password' => $this->password,
                'forceChange' => $this->forceChange,
                'loginUrl' => url('/login'),
                'appName' => config('app.name'),
            ],
        );
    }

    /**
     * Remove serialized password from queue failure logs.
     */
    public function failed(\Throwable $exception): void
    {
        // Scrub secrets from any downstream logging.
        $this->password = '[redacted]';
    }
}
