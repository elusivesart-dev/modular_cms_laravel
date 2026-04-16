<?php

declare(strict_types=1);

return [
    'title' => 'Permissions',
    'single' => 'Permission',
    'empty' => 'No permissions found.',
    'create_title' => 'Create Permission',
    'edit_title' => 'Edit Permission',

    'fields.name' => 'Name',
    'fields.label' => 'Label',
    'fields.description' => 'Description',
    'fields.roles' => 'Roles',
    'fields.actions' => 'Actions',
    'fields.localized_label' => 'Localized label',
    'fields.localized_description' => 'Localized description',
    'fields.system_label' => 'System label key / fallback label',
    'fields.system_description' => 'System fallback description',

    'actions.create' => 'Create',
    'actions.edit' => 'Edit',
    'actions.delete' => 'Delete',
    'actions.save' => 'Save',
    'actions.update' => 'Update',
    'actions.back' => 'Back',

    'messages.created' => 'Permission created successfully.',
    'messages.updated' => 'Permission updated successfully.',
    'messages.deleted' => 'Permission deleted successfully.',
    'messages.confirm_delete' => 'Are you sure you want to delete this permission?',

    'help.name' => 'Use module.action format',
    'help.label' => 'For system permissions use a translation key. For custom permissions you may leave it empty and fill the localized labels.',
    'help.description' => 'Fallback description used when the active language has no translation.',

    'translations_title' => 'Translations',

    'validation.label_required' => 'Provide a label for at least one language or enter a system label key.',

    'items.users.view' => 'View users',
    'items.users.create' => 'Create users',
    'items.users.update' => 'Update users',
    'items.users.delete' => 'Delete users',

    'items.roles.view' => 'View roles',
    'items.roles.create' => 'Create roles',
    'items.roles.update' => 'Update roles',
    'items.roles.delete' => 'Delete roles',

    'items.permissions.view' => 'View permissions',
    'items.permissions.create' => 'Create permissions',
    'items.permissions.update' => 'Update permissions',
    'items.permissions.delete' => 'Delete permissions',

    'items.settings.view' => 'View settings',
    'items.settings.create' => 'Create settings',
    'items.settings.update' => 'Update settings',
    'items.settings.delete' => 'Delete settings',

    'items.localization.view' => 'View localization',
    'items.localization.manage' => 'Manage localization',
    'items.localization.install' => 'Install language',
    'items.localization.delete' => 'Delete language',
    'items.localization.update' => 'Update language',

    'items.themes.view' => 'View themes',
    'items.themes.update' => 'Update themes',
    'items.themes.manage' => 'Manage themes',
];