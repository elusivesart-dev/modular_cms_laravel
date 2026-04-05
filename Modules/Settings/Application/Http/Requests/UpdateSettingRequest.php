<?php

declare(strict_types=1);

namespace Modules\Settings\Application\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Settings\Infrastructure\Models\Setting;

final class UpdateSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        $setting = $this->route('setting');

        return $setting instanceof Setting
            ? ($this->user()?->can('update', $setting) ?? false)
            : false;
    }

    public function rules(): array
    {
        $setting = $this->route('setting');

        return [
            'group' => ['required', 'string', 'max:120'],
            'key' => [
                'required',
                'string',
                'max:150',
                Rule::unique('settings', 'key')->ignore($setting->getKey()),
            ],
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