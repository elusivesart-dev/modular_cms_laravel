<?php

declare(strict_types=1);

return [
    'verify' => [
        'subject' => 'Email Verification | :app',
        'generic_user' => 'user',
        'greeting' => 'Hello, :name!',
        'intro' => 'Thank you for registering at :app.',
        'instructions' => 'Click the button below to verify your email address and activate your account.',
        'action' => 'Verify Email',
        'expiration' => 'This link is valid for :minutes minutes.',
        'ignore' => 'If you did not create an account, you can ignore this message.',
        'fallback' => 'If the button does not work, use the following link:',
        'salutation' => 'Regards, :app',
        'footer' => 'This is an automated message sent by :app.',
    ],
];
