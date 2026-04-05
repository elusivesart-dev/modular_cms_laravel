<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('users::mail.verify.subject', ['app' => $applicationName]) }}</title>
</head>
<body style="margin:0;padding:0;background:#f4f6f9;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
<table width="100%" cellpadding="0" cellspacing="0" style="padding:30px 0;background:#f4f6f9;">
    <tr>
        <td align="center">
            <table width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;background:#ffffff;border-radius:10px;overflow:hidden;border:1px solid #e5e7eb;">
                <tr>
                    <td style="background:#111827;padding:25px;text-align:center;">
                        <h1 style="color:#ffffff;margin:0;font-size:22px;">{{ $applicationName }}</h1>

                    </td>
                </tr>

                <tr>
                    <td style="padding:30px;">
                        <h2 style="margin:0 0 15px 0;font-size:18px;">
                            {{ __('users::mail.verify.greeting', ['name' => $userName !== '' ? $userName : __('users::mail.verify.generic_user')]) }}
                        </h2>

                        @if (!empty($userEmail))
                            <p style="margin:0 0 15px 0;font-size:14px;color:#374151;">
                                {{ $userEmail }}
                            </p>
                        @endif

                        <p style="margin:0 0 15px 0;font-size:14px;">
                            {{ __('users::mail.verify.intro', ['app' => $applicationName]) }}
                        </p>

                        <p style="margin:0 0 25px 0;font-size:14px;">
                            {{ __('users::mail.verify.instructions') }}
                        </p>

                        <table cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td>
                                    <a href="{{ $verificationUrl }}" style="display:inline-block;padding:12px 24px;background:#111827;color:#ffffff;text-decoration:none;border-radius:6px;font-weight:bold;">
                                        {{ __('users::mail.verify.action') }}
                                    </a>
                                </td>
                            </tr>
                        </table>

                        <p style="margin:25px 0 10px 0;font-size:13px;color:#6b7280;">
                            {{ __('users::mail.verify.expiration', ['minutes' => $expirationMinutes]) }}
                        </p>

                        <p style="margin:0 0 10px 0;font-size:13px;color:#6b7280;">
                            {{ __('users::mail.verify.ignore') }}
                        </p>

                        <p style="margin:15px 0 5px 0;font-size:13px;color:#6b7280;">
                            {{ __('users::mail.verify.fallback') }}
                        </p>

                        <p style="word-break:break-all;font-size:12px;color:#2563eb;">
                            <a href="{{ $verificationUrl }}" style="color:#2563eb;text-decoration:none;">
                                {{ $verificationUrl }}
                            </a>
                        </p>

                        <p style="margin-top:25px;font-size:13px;color:#6b7280;">
                            {{ __('users::mail.verify.salutation', ['app' => $mailFromName ?: $applicationName]) }}
                        </p>
                    </td>
                </tr>

                <tr>
                    <td style="padding:20px;text-align:center;background:#f9fafb;border-top:1px solid #e5e7eb;font-size:12px;color:#9ca3af;">
                        {{ __('users::mail.verify.footer', ['app' => $applicationName]) }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>