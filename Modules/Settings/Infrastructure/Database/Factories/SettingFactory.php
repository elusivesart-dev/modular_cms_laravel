<?php

declare(strict_types=1);

namespace Modules\Settings\Infrastructure\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Settings\Infrastructure\Models\Setting;

/**
 * @extends Factory<Setting>
 */
final class SettingFactory extends Factory
{
    protected $model = Setting::class;

    public function definition(): array
    {
        $group = $this->faker->randomElement(['general', 'system', 'mail', 'seo']);

        return [
            'group' => $group,
            'key' => $group . '.' . $this->faker->unique()->slug(2, '_'),
            'value' => $this->faker->word(),
            'type' => $this->faker->randomElement(['string', 'text', 'integer', 'boolean', 'json']),
            'label' => $this->faker->sentence(2),
            'description' => $this->faker->sentence(),
            'is_public' => false,
            'is_system' => false,
        ];
    }
}