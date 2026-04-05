<?php

declare(strict_types=1);

namespace Modules\Media\Infrastructure\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Media\Infrastructure\Models\Media;

/**
 * @extends Factory<Media>
 */
final class MediaFactory extends Factory
{
    protected $model = Media::class;

    public function definition(): array
    {
        return [
            'disk' => 'public',
            'directory' => 'media/' . now()->format('Y/m'),
            'path' => 'media/' . now()->format('Y/m') . '/' . $this->faker->uuid() . '.jpg',
            'filename' => $this->faker->uuid() . '.jpg',
            'original_name' => 'image.jpg',
            'mime_type' => 'image/jpeg',
            'extension' => 'jpg',
            'size' => 102400,
            'visibility' => 'public',
            'title' => $this->faker->sentence(3),
            'alt_text' => $this->faker->sentence(4),
            'uploaded_by' => null,
        ];
    }
}