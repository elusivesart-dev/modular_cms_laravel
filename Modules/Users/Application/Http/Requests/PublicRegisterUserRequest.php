<?php

declare(strict_types=1);

namespace Modules\Users\Application\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

final class PublicRegisterUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() === null;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'email:rfc', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:' . (string) config('users.password_min_length', 8), 'max:255', 'confirmed'],
            'g-recaptcha-response' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array{name:string,email:string,password:string}
     */
    public function validatedPayload(): array
    {
        $this->validateCaptcha();

        $data = $this->validated();

        return [
            'name' => trim(strip_tags((string) $data['name'])),
            'email' => mb_strtolower(trim((string) $data['email'])),
            'password' => (string) $data['password'],
        ];
    }

    private function validateCaptcha(): void
    {
        if (!(bool) config('auth-module.registration.captcha.enabled', false)) {
            return;
        }

        $provider = mb_strtolower((string) config('auth-module.registration.captcha.provider', 'recaptcha_v3'));

        if ($provider !== 'recaptcha_v3' && $provider !== 'google_recaptcha_v3') {
            return;
        }

        $secret = trim((string) config('auth-module.registration.captcha.secret', ''));
        $token = trim((string) $this->input('g-recaptcha-response', ''));
        $expectedAction = trim((string) config('auth-module.registration.captcha.action', 'register'));
        $minimumScore = (float) config('auth-module.registration.captcha.minimum_score', 0.5);

        if ($secret === '') {
            throw ValidationException::withMessages([
                'g-recaptcha-response' => __('auth-module::auth.captcha_failed'),
            ]);
        }

        if ($token === '') {
            throw ValidationException::withMessages([
                'g-recaptcha-response' => __('auth-module::auth.captcha_required'),
            ]);
        }

        $response = Http::asForm()
            ->acceptJson()
            ->timeout(10)
            ->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secret,
                'response' => $token,
                'remoteip' => $this->ip(),
            ]);

        if (!$response->ok()) {
            throw ValidationException::withMessages([
                'g-recaptcha-response' => __('auth-module::auth.captcha_failed'),
            ]);
        }

        $payload = $response->json();

        if (!is_array($payload)) {
            throw ValidationException::withMessages([
                'g-recaptcha-response' => __('auth-module::auth.captcha_failed'),
            ]);
        }

        $success = (bool) ($payload['success'] ?? false);
        $score = isset($payload['score']) ? (float) $payload['score'] : 0.0;
        $action = isset($payload['action']) ? trim((string) $payload['action']) : '';
        $hostname = isset($payload['hostname']) ? mb_strtolower(trim((string) $payload['hostname'])) : '';

        if (!$success) {
            throw ValidationException::withMessages([
                'g-recaptcha-response' => __('auth-module::auth.captcha_failed'),
            ]);
        }

        if ($expectedAction !== '' && $action !== '' && $action !== $expectedAction) {
            throw ValidationException::withMessages([
                'g-recaptcha-response' => __('auth-module::auth.captcha_failed'),
            ]);
        }

        if ($score < $minimumScore) {
            throw ValidationException::withMessages([
                'g-recaptcha-response' => __('auth-module::auth.captcha_failed'),
            ]);
        }

        $expectedHostnames = array_values(array_filter(array_map(
            static fn (mixed $value): string => mb_strtolower(trim((string) $value)),
            (array) config('auth-module.registration.captcha.allowed_hostnames', [])
        )));

        if ($expectedHostnames !== [] && $hostname !== '' && !in_array($hostname, $expectedHostnames, true)) {
            throw ValidationException::withMessages([
                'g-recaptcha-response' => __('auth-module::auth.captcha_failed'),
            ]);
        }
    }
}