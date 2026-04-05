<?php

declare(strict_types=1);

namespace Modules\Settings\Application\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', \Modules\Settings\Infrastructure\Models\Setting::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'group' => ['required', 'string', 'max:120'],
            'key' => ['required', 'string', 'max:150', 'unique:settings,key'],
            'value' => ['nullable'],
            'type' => ['required', 'string'],
            'label' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'is_public' => ['nullable', 'boolean'],
            'is_system' => ['nullable', 'boolean'],
        ];
    }

    public function validatedPayload(): array
    {
        $data = $this->validated();
        $data['is_public'] = (bool) ($data['is_public'] ?? false);
        $data['is_system'] = (bool) ($data['is_system'] ?? false);

        return $data;
    }
}