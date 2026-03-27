<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;

class VerifyEmailCustom extends VerifyEmail
{
    /**
     * Genera el email de verificación adaptado para API.
     */
    public function toMail($notifiable)
    {
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );

        return (new MailMessage)
            ->subject('Verifica tu correo electrónico')
            ->line('Haz clic en el siguiente botón para verificar tu correo:')
            ->action('Verificar Email', $verificationUrl)
            ->line('Si no creaste esta cuenta, puedes ignorar este mensaje.');
    }
}
