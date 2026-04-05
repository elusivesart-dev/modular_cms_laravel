<?php

declare(strict_types=1);

return [
    'title' => 'Потребители',
    'create' => 'Създаване на потребител',
    'edit' => 'Редакция на потребител',
    'show' => 'Преглед на потребител',

    'name' => 'Име',
    'email' => 'Имейл',
    'password' => 'Парола',
    'password_confirmation' => 'Потвърждение на паролата',
    'is_active' => 'Активен',
    'roles' => 'Роли',
    'id' => 'ID',
    'actions' => 'Действия',

    'save' => 'Запази',
    'update' => 'Обнови',
    'delete' => 'Изтрий',
    'back' => 'Назад',
    'view' => 'Преглед',

    'created' => 'Потребителят е създаден успешно.',
    'updated' => 'Потребителят е обновен успешно.',
    'deleted' => 'Потребителят е изтрит успешно.',
    'no_records' => 'Няма намерени потребители.',
    'delete_confirm' => 'Сигурен ли си, че искаш да изтриеш този потребител?',

    'yes' => 'Да',
    'no' => 'Не',

    'exceptions' => [
        'user_already_exists' => 'Потребител с имейл ":email" вече съществува.',
        'user_not_found' => 'Потребител с ID ":id" не беше намерен.',
        'cannot_assign_super_admin' => 'Само super-admin може да присвоява ролята super-admin.',
        'last_super_admin_cannot_be_deleted' => 'Последният super-admin потребител не може да бъде изтрит.',
        'cannot_delete_self' => 'Не можете да изтриете собствения си акаунт.',
    ],

    'media' => [
        'choose_from_library' => 'Избери от медийната библиотека',
        'invalid_avatar_selection' => 'Избраният файл не е валидно изображение за аватар.',
    ],

    'public' => [
        'register' => 'Регистрация',
        'register_submit' => 'Създай профил',
        'registration_success' => 'Профилът беше създаден успешно. Провери имейла си за активация.',
        'profile_updated' => 'Профилът беше обновен успешно.',
        'email_verified' => 'Имейлът беше потвърден успешно!',

        'my_profile' => 'Моят профил',
        'edit_profile' => 'Редакция на профила',
        'admin_panel' => 'Админ Панел',
        'logout' => 'Изход',

        'password_confirmation' => 'Повтори паролата',
        'current_password_required' => 'Текуща парола за потвърждение',
        'new_password' => 'Нова парола',

        'slug' => 'Слъг',
        'bio' => 'Биография',
        'avatar' => 'Аватар',
        'joined_at' => 'Регистриран на',
        'status' => 'Статус',
        'active' => 'Активен',
        'inactive' => 'Неактивен',

        'recaptcha_notice_html' => 'Този сайт е защитен с reCAPTCHA и важат :privacy_policy и :terms_of_service на Google.',
        'privacy_policy' => 'Политиката за поверителност',
        'terms_of_service' => 'Общите условия',
		'email_already_verified' => 'Този имейл адрес вече е потвърден.',
    ],

    'verify' => [
        'subject' => 'Потвърждение на имейл | :app',
        'generic_user' => 'потребителю',
        'greeting' => 'Здравей, :name!',
        'intro' => 'Получихме регистрация в :app.',
        'instructions' => 'Натисни бутона по-долу, за да потвърдиш имейла си.',
        'action' => 'Потвърди имейла',
        'expiration' => 'Линкът е валиден :minutes минути.',
        'ignore' => 'Ако не си създавал акаунт – игнорирай това съобщение.',
        'fallback' => 'Ако бутонът не работи, използвай линка:',
        'salutation' => 'Поздрави, :app',
        'footer' => 'Автоматично съобщение от :app',
    ],
];