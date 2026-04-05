<?php

declare(strict_types=1);

return [
    'middleware' => ['web'],

    'redirect_after_login' => 'home',

    'login_throttle' => [
        'max_attempts' => 5,
        'decay_seconds' => 60,
    ],

    'registration' => [
        'captcha' => [
            'enabled' => (bool) env('AUTH_REGISTRATION_CAPTCHA_ENABLED', false),
            'provider' => (string) env('AUTH_REGISTRATION_CAPTCHA_PROVIDER', 'recaptcha_v3'),
            'site_key' => (string) env('AUTH_REGISTRATION_CAPTCHA_SITE_KEY', ''),
            'secret' => (string) env('AUTH_REGISTRATION_CAPTCHA_SECRET', ''),
            'action' => (string) env('AUTH_REGISTRATION_CAPTCHA_ACTION', 'register'),
            'minimum_score' => (float) env('AUTH_REGISTRATION_CAPTCHA_MINIMUM_SCORE', 0.5),
            'allowed_hostnames' => array_values(array_filter(array_map(
                static fn (string $hostname): string => trim($hostname),
                explode(',', (string) env('AUTH_REGISTRATION_CAPTCHA_ALLOWED_HOSTNAMES', ''))
            ))),
        ],
    ],
];