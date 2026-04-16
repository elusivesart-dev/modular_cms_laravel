<?php

declare(strict_types=1);

return [
    'title' => 'Права',
    'single' => 'Право',
    'empty' => 'Няма намерени права.',
    'create_title' => 'Създаване на право',
    'edit_title' => 'Редакция на право',

    'fields.name' => 'Име',
    'fields.label' => 'Етикет',
    'fields.description' => 'Описание',
    'fields.roles' => 'Роли',
    'fields.actions' => 'Действия',
    'fields.localized_label' => 'Етикет за езика',
    'fields.localized_description' => 'Описание за езика',
    'fields.system_label' => 'Системен label key / fallback етикет',
    'fields.system_description' => 'Системно fallback описание',

    'actions.create' => 'Създаване',
    'actions.edit' => 'Редакция',
    'actions.delete' => 'Изтриване',
    'actions.save' => 'Запази',
    'actions.update' => 'Обнови',
    'actions.back' => 'Назад',

    'messages.created' => 'Правото беше създадено успешно.',
    'messages.updated' => 'Правото беше обновено успешно.',
    'messages.deleted' => 'Правото беше изтрито успешно.',
    'messages.confirm_delete' => 'Сигурен ли си, че искаш да изтриеш това право?',

    'help.name' => 'Използвай формат module.action',
    'help.label' => 'За системни права използвай translation key. За custom права можеш да го оставиш празно и да попълниш преводите по езици.',
    'help.description' => 'Поле за fallback описание, когато няма превод за активния език.',

    'translations_title' => 'Преводи',

    'validation.label_required' => 'Попълни етикет поне за един език или въведи системен label key.',

    'items.users.view' => 'Преглед на потребители',
    'items.users.create' => 'Създаване на потребители',
    'items.users.update' => 'Редакция на потребители',
    'items.users.delete' => 'Изтриване на потребители',

    'items.roles.view' => 'Преглед на роли',
    'items.roles.create' => 'Създаване на роли',
    'items.roles.update' => 'Редакция на роли',
    'items.roles.delete' => 'Изтриване на роли',

    'items.permissions.view' => 'Преглед на права',
    'items.permissions.create' => 'Създаване на права',
    'items.permissions.update' => 'Редакция на права',
    'items.permissions.delete' => 'Изтриване на права',

    'items.settings.view' => 'Преглед на настройки',
    'items.settings.create' => 'Създаване на настройки',
    'items.settings.update' => 'Редакция на настройки',
    'items.settings.delete' => 'Изтриване на настройки',

    'items.localization.view' => 'Преглед на локализация',
    'items.localization.manage' => 'Управление на локализация',
    'items.localization.install' => 'Инсталиране на език',
    'items.localization.delete' => 'Изтриване на език',
    'items.localization.update' => 'Обновяване на език',

    'items.themes.view' => 'Преглед на теми',
    'items.themes.update' => 'Редакция на теми',
    'items.themes.manage' => 'Управление на теми',
];