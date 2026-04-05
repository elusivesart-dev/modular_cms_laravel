<?php

declare(strict_types=1);

namespace Modules\Settings\Infrastructure\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Settings\Infrastructure\Models\Setting;

final class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'group' => 'general',
                'key' => 'general.site_name',
                'value' => 'MCMS',
                'type' => 'string',
                'label' => 'Име на сайта',
                'description' => 'Основното име, което се използва в административния панел и публичната част.',
                'is_public' => true,
                'is_system' => false,
            ],
            [
                'group' => 'general',
                'key' => 'general.site_tagline',
                'value' => 'Enterprise Modular CMS',
                'type' => 'string',
                'label' => 'Слоган',
                'description' => 'Кратък подзаглавен текст за сайта.',
                'is_public' => true,
                'is_system' => false,
            ],
            [
                'group' => 'contact',
                'key' => 'contact.email',
                'value' => 'admin@mcms.local',
                'type' => 'string',
                'label' => 'Имейл за контакт',
                'description' => 'Основен имейл за връзка.',
                'is_public' => true,
                'is_system' => false,
            ],
            [
                'group' => 'contact',
                'key' => 'contact.phone',
                'value' => '',
                'type' => 'string',
                'label' => 'Телефон',
                'description' => 'Основен телефон за контакт.',
                'is_public' => true,
                'is_system' => false,
            ],
            [
                'group' => 'contact',
                'key' => 'contact.address',
                'value' => '',
                'type' => 'text',
                'label' => 'Адрес',
                'description' => 'Адрес за контакт или офис.',
                'is_public' => true,
                'is_system' => false,
            ],
            [
                'group' => 'seo',
                'key' => 'seo.meta_title',
                'value' => 'MCMS',
                'type' => 'string',
                'label' => 'Meta title',
                'description' => 'Основно SEO заглавие на сайта.',
                'is_public' => true,
                'is_system' => false,
            ],
            [
                'group' => 'seo',
                'key' => 'seo.meta_description',
                'value' => '',
                'type' => 'text',
                'label' => 'Meta description',
                'description' => 'Основно SEO описание на сайта.',
                'is_public' => true,
                'is_system' => false,
            ],
            [
                'group' => 'mail',
                'key' => 'mail.from_name',
                'value' => 'MCMS',
                'type' => 'string',
                'label' => 'Име на изпращача',
                'description' => 'Име, което се вижда при изпращане на имейли.',
                'is_public' => false,
                'is_system' => false,
            ],
            [
                'group' => 'mail',
                'key' => 'mail.from_address',
                'value' => 'no-reply@mcms.local',
                'type' => 'string',
                'label' => 'Имейл на изпращача',
                'description' => 'Имейл адресът, от който се изпращат системни писма.',
                'is_public' => false,
                'is_system' => false,
            ],
            [
                'group' => 'system',
                'key' => 'system.default_locale',
                'value' => 'bg',
                'type' => 'string',
                'label' => 'Език по подразбиране',
                'description' => 'Системна настройка за език по подразбиране.',
                'is_public' => false,
                'is_system' => true,
            ],
            [
                'group' => 'system',
                'key' => 'system.timezone',
                'value' => 'Europe/Sofia',
                'type' => 'string',
                'label' => 'Часова зона',
                'description' => 'Системна настройка за часова зона.',
                'is_public' => false,
                'is_system' => true,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::query()->updateOrCreate(
                ['key' => $setting['key']],
                [
                    'group' => $setting['group'],
                    'value' => $setting['value'],
                    'type' => $setting['type'],
                    'label' => $setting['label'],
                    'description' => $setting['description'],
                    'is_public' => $setting['is_public'],
                    'is_system' => $setting['is_system'],
                ]
            );
        }

        $this->call(SettingsPermissionSeeder::class);
    }
}