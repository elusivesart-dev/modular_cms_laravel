<?php

declare(strict_types=1);

namespace Modules\Users\Application\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

final class VerifyEmailNotification extends VerifyEmail
{
    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        app()->setLocale($this->locale ?? config('app.locale'));

        $verificationUrl = $this->verificationUrl($notifiable);
        $applicationName = (string) config('app.name', 'MCMS');
        $mailFromName = trim((string) config('mail.from.name', $applicationName));
        $mailFromAddress = trim((string) config('mail.from.address', ''));
        $expirationMinutes = (int) config('auth.verification.expire', 60);

        $message = (new MailMessage)
            ->subject(__('users::mail.verify.subject', ['app' => $applicationName]))
            ->view('users::users.public.verify-email', [
                'userName' => (string) ($notifiable->name ?? ''),
                'userEmail' => (string) ($notifiable->email ?? ''),
                'verificationUrl' => $verificationUrl,
                'applicationName' => $applicationName,
                'mailFromName' => $mailFromName !== '' ? $mailFromName : $applicationName,
                'mailFromAddress' => $mailFromAddress,
                'expirationMinutes' => $expirationMinutes,
            ]);

        if ($mailFromAddress !== '') {
            $message->from(
                $mailFromAddress,
                $mailFromName !== '' ? $mailFromName : $applicationName
            );
        }

        return $message;
    }
}