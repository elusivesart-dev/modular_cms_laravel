<?php

declare(strict_types=1);

return [
    'title' => 'Media',
    'upload_title' => 'Upload file',
    'library_title' => 'Media library',

    'picker' => [
        'title' => 'Select file',
        'library_title' => 'Select from media library',
    ],

    'fields' => [
        'file' => 'File',
        'title' => 'Title',
        'alt_text' => 'Alt text',
    ],

    'filters' => [
        'search' => 'Search',
        'type' => 'Type',
        'all' => 'All',
    ],

    'types' => [
        'image' => 'Images',
        'video' => 'Video',
        'audio' => 'Audio',
        'document' => 'Documents',
    ],

    'actions' => [
        'upload' => 'Upload',
        'delete' => 'Delete',
        'open' => 'Open',
        'filter' => 'Filter',
        'select' => 'Select',
    ],

    'messages' => [
        'uploaded' => 'The file was uploaded successfully.',
        'deleted' => 'The file was deleted successfully.',
        'upload_failed' => 'A problem occurred while uploading the file.',
        'delete_failed' => 'A problem occurred while deleting the file.',
        'delete_confirm' => 'Are you sure you want to delete this file?',
        'no_records' => 'No uploaded files found.',
    ],

    'hints' => [
        'allowed_types' => 'Allowed extensions',
        'max_size' => 'Maximum size',
    ],

    'permissions' => [
        'view' => 'View media',
        'create' => 'Upload media',
        'delete' => 'Delete media',
    ],
];