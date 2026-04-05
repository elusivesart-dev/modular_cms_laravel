<?php

declare(strict_types=1);

namespace Modules\Users\Infrastructure\Models;

use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Modules\Media\Infrastructure\Models\Media;
use Modules\Users\Application\Notifications\VerifyEmailNotification;
use Modules\Users\Infrastructure\Database\Factories\UserFactory;

final class User extends Authenticatable implements MustVerifyEmailContract
{
    use HasApiTokens;
    use HasFactory;
    use MustVerifyEmail;
    use Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'slug',
        'email',
        'password',
        'bio',
        'avatar_path',
        'avatar_media_id',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'avatar_media_id' => 'integer',
        ];
    }

    public function avatarMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'avatar_media_id');
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->relationLoaded('avatarMedia') && $this->avatarMedia !== null && $this->avatarMedia->url !== null) {
            return $this->avatarMedia->url;
        }

        if ($this->avatarMedia !== null && $this->avatarMedia->url !== null) {
            return $this->avatarMedia->url;
        }

        if (!empty($this->avatar_path)) {
            return url('storage/' . ltrim((string) $this->avatar_path, '/'));
        }

        return admin_theme_asset('images/avatar-4.jpg');
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(
            (new VerifyEmailNotification())
                ->locale(app()->getLocale())
        );
    }

    public function preferredLocale(): string
    {
        if (property_exists($this, 'locale') && is_string($this->locale) && $this->locale !== '') {
            return $this->locale;
        }

        return (string) config('app.locale', 'en');
    }

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }
}