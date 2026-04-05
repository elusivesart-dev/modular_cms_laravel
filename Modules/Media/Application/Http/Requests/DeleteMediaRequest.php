<?php

declare(strict_types=1);

namespace Modules\Media\Application\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Media\Infrastructure\Models\Media;

final class DeleteMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        $media = $this->route('media');

        return $media instanceof Media
            ? ($this->user()?->can('delete', $media) ?? false)
            : false;
    }

    public function rules(): array
    {
        return [];
    }
}