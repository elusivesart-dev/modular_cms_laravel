<?php

declare(strict_types=1);

namespace Modules\Settings\Application\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Settings\Infrastructure\Models\Setting;

final class UpdateSettingGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('viewAny', Setting::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'group' => ['sometimes', 'string', 'max:120'],
            'values' => ['required', 'array'],
        ];
    }

    /**
     * @return array{group:string,values:array<string,mixed>}
     */
    public function validatedPayload(): array
    {
        $data = $this->validated();

        return [
            'group' => (string) ($data['group'] ?? ''),
            'values' => is_array($data['values']) ? $data['values'] : [],
        ];
    }
}