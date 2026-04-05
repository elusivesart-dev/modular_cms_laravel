<?php

declare(strict_types=1);

namespace Modules\Media\Application\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Media\Infrastructure\Models\Media;

final class UploadMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Media::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                'max:' . (int) config('media.max_file_size', 10240),
                'mimetypes:' . implode(',', (array) config('media.allowed_mime_types', [])),
                'extensions:' . implode(',', (array) config('media.allowed_extensions', [])),
            ],
            'title' => ['nullable', 'string', 'max:255'],
            'alt_text' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array{title:?string,alt_text:?string}
     */
    public function validatedPayload(): array
    {
        $data = $this->validated();

        return [
            'title' => isset($data['title']) ? trim((string) $data['title']) : null,
            'alt_text' => isset($data['alt_text']) ? trim((string) $data['alt_text']) : null,
        ];
    }
}