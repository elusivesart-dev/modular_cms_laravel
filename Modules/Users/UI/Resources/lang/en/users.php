<?php

declare(strict_types=1);

return [
    'title' => 'Users',
    'create' => 'Create user',
    'edit' => 'Edit user',
    'show' => 'View user',

    'name' => 'Name',
    'email' => 'Email',
    'password' => 'Password',
    'password_confirmation' => 'Password confirmation',
    'is_active' => 'Active',
    'roles' => 'Roles',
    'id' => 'ID',
    'actions' => 'Actions',

    'save' => 'Save',
    'update' => 'Update',
    'delete' => 'Delete',
    'back' => 'Back',
    'view' => 'View',

    'created' => 'The user was created successfully.',
    'updated' => 'The user was updated successfully.',
    'deleted' => 'The user was deleted successfully.',
    'no_records' => 'No users found.',
    'delete_confirm' => 'Are you sure you want to delete this user?',

    'yes' => 'Yes',
    'no' => 'No',

    'exceptions' => [
        'user_already_exists' => 'A user with email ":email" already exists.',
        'user_not_found' => 'User with ID ":id" was not found.',
        'cannot_assign_super_admin' => 'Only super-admin can assign the super-admin role.',
        'last_super_admin_cannot_be_deleted' => 'The last super-admin user cannot be deleted.',
        'cannot_delete_self' => 'You cannot delete your own account.',
    ],

    'media' => [
        'choose_from_library' => 'Choose from media library',
        'invalid_avatar_selection' => 'The selected file is not a valid avatar image.',
    ],

    'public' => [
        'register' => 'Register',
        'register_submit' => 'Create account',
        'registration_success' => 'Your account was created successfully. Check your email for activation.',
        'profile_updated' => 'Profile updated successfully.',
        'email_verified' => 'Email verified successfully!',

        'my_profile' => 'My profile',
        'edit_profile' => 'Edit profile',
        'admin_panel' => 'Admin panel',
        'logout' => 'Logout',

        'password_confirmation' => 'Confirm password',
        'current_password_required' => 'Current password required for confirmation',
        'new_password' => 'New password',

        'slug' => 'Slug',
        'bio' => 'Bio',
        'avatar' => 'Avatar',
        'joined_at' => 'Joined at',
        'status' => 'Status',
        'active' => 'Active',
        'inactive' => 'Inactive',

        'recaptcha_notice_html' => 'This site is protected by reCAPTCHA and the Google :privacy_policy and :terms_of_service apply.',
        'privacy_policy' => 'Privacy Policy',
        'terms_of_service' => 'Terms of Service',
		'email_already_verified' => 'This Email adress is already confirmed.',
    ],

    'verify' => [
        'subject' => 'Email Verification | :app',
        'generic_user' => 'user',
        'greeting' => 'Hello, :name!',
        'intro' => 'We received a registration for :app.',
        'instructions' => 'Click the button below to verify your email.',
        'action' => 'Verify Email',
        'expiration' => 'This link is valid for :minutes minutes.',
        'ignore' => 'If you did not create an account, ignore this message.',
        'fallback' => 'If the button does not work, use the link:',
        'salutation' => 'Regards, :app',
        'footer' => 'Automated message from :app',
    ],
];