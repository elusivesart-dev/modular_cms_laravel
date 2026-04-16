<?php

declare(strict_types=1);

return [
    'title' => 'Роли',
    'create' => 'Създаване на роля',
    'edit' => 'Редакция на роля',
    'show' => 'Преглед на роля',
    'name' => 'Име',
    'slug' => 'Ключ',
    'description' => 'Описание',
    'is_system' => 'Системна роля',
    'permissions' => 'Права',
    'actions' => 'Действия',
    'save' => 'Запази',
    'update' => 'Обнови',
    'delete' => 'Изтрий',
    'created' => 'Ролята е създадена успешно.',
    'updated' => 'Ролята е обновена успешно.',
    'deleted' => 'Ролята е изтрита успешно.',
    'back' => 'Назад',
    'assignments' => 'Назначения',
    'no_records' => 'Няма намерени роли.',
    'no_permissions_found' => 'Няма намерени права.',
    'no_permissions_assigned' => 'Няма присвоени права.',
    'yes' => 'Да',
    'no' => 'Не',
    'view' => 'Преглед',
    'translations_title' => 'Преводи',
    'localized_name' => 'Име за езика',
    'localized_description' => 'Описание за езика',
    'slug_help' => 'Само малки латински букви, цифри и тире. Пример: content-manager',
    'validation' => [
        'role_name_required' => 'Попълни име на ролята поне за един език.',
    ],
    'items' => [
        'super-admin' => [
            'name' => 'Супер администратор',
            'description' => 'Пълен административен достъп.',
        ],
        'admin' => [
            'name' => 'Администратор',
            'description' => 'Административен достъп.',
        ],
        'editor' => [
            'name' => 'Редактор',
            'description' => 'Достъп за редакция на съдържание.',
        ],
    ],
    'exceptions' => [
        'role_not_found' => 'Ролята ":role" не беше намерена.',
    ],
];