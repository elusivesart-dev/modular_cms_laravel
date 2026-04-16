<?php

declare(strict_types=1);

return [
    'title' => 'Roles',
    'create' => 'Create Role',
    'edit' => 'Edit Role',
    'show' => 'View Role',
    'name' => 'Name',
    'slug' => 'Slug',
    'description' => 'Description',
    'is_system' => 'System Role',
    'permissions' => 'Permissions',
    'actions' => 'Actions',
    'save' => 'Save',
    'update' => 'Update',
    'delete' => 'Delete',
    'created' => 'Role created successfully.',
    'updated' => 'Role updated successfully.',
    'deleted' => 'Role deleted successfully.',
    'back' => 'Back',
    'assignments' => 'Assignments',
    'no_records' => 'No roles found.',
    'no_permissions_found' => 'No permissions found.',
    'no_permissions_assigned' => 'No permissions assigned.',
    'yes' => 'Yes',
    'no' => 'No',
    'view' => 'View',
    'translations_title' => 'Translations',
    'localized_name' => 'Localized name',
    'localized_description' => 'Localized description',
    'slug_help' => 'Only lowercase Latin letters, numbers, and hyphens. Example: content-manager',
    'validation' => [
        'role_name_required' => 'Provide a role name for at least one language.',
    ],
    'items' => [
        'super-admin' => [
            'name' => 'Super Administrator',
            'description' => 'Full administrative access.',
        ],
        'admin' => [
            'name' => 'Administrator',
            'description' => 'Administrative access.',
        ],
        'editor' => [
            'name' => 'Editor',
            'description' => 'Content editing access.',
        ],
    ],
    'exceptions' => [
        'role_not_found' => 'Role ":role" was not found.',
    ],
];