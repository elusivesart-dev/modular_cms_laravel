<?php

declare(strict_types=1);

namespace Modules\Audit\Application\Support;

use App\Core\Audit\Models\AuditLog;
use Illuminate\Support\Facades\Lang;

final class AuditLogFormatter
{
    /**
     * @return array{
     *     id:int,
     *     author:string,
     *     action:string,
     *     user:string,
     *     role:string,
     *     ip_address:string,
     *     created_at:string,
     *     details:array<int, string>,
     *     model:AuditLog
     * }
     */
    public function format(AuditLog $log): array
    {
        return [
            'id' => (int) $log->id,
            'author' => $this->resolveAuthor($log),
            'action' => $this->resolveAction($log),
            'user' => $this->resolveUser($log),
            'role' => $this->resolveRole($log),
            'ip_address' => $this->resolveIpAddress($log),
            'created_at' => $this->resolveCreatedAt($log),
            'details' => $this->resolveDetails($log),
            'model' => $log,
        ];
    }

    private function resolveAuthor(AuditLog $log): string
    {
        if ($log->actor !== null) {
            $name = $this->stringValue($log->actor->name ?? null);
            $email = $this->stringValue($log->actor->email ?? null);

            if ($name !== '' && $email !== '') {
                return $name . ' (' . $email . ')';
            }

            if ($name !== '') {
                return $name;
            }

            if ($email !== '') {
                return $email;
            }
        }

        return (string) __('audit::audit.system');
    }

    private function resolveAction(AuditLog $log): string
    {
        $event = trim((string) ($log->event ?? ''));
        $description = trim((string) ($log->description ?? ''));

        if ($event !== '') {
            $translations = Lang::get('audit::audit.events');

            if (is_array($translations) && array_key_exists($event, $translations)) {
                $translated = $translations[$event];

                if (is_string($translated) && $translated !== '') {
                    return $translated;
                }
            }
        }

        if ($description !== '') {
            return $description;
        }

        return (string) __('audit::audit.unknown');
    }

    private function resolveUser(AuditLog $log): string
    {
        $properties = $this->properties($log);

        $user = trim((string) ($properties['user'] ?? ''));
        $email = trim((string) ($properties['email'] ?? ''));

        if ($user !== '' && $email !== '') {
            return $user . ' (' . $email . ')';
        }

        if ($email !== '') {
            return $email;
        }

        if ($user !== '') {
            return $user;
        }

        if ($log->subject !== null) {
            $subjectName = $this->stringValue($log->subject->name ?? null);
            $subjectEmail = $this->stringValue($log->subject->email ?? null);

            if ($subjectName !== '' && $subjectEmail !== '') {
                return $subjectName . ' (' . $subjectEmail . ')';
            }

            if ($subjectEmail !== '') {
                return $subjectEmail;
            }

            if ($subjectName !== '') {
                return $subjectName;
            }
        }

        return '—';
    }

    private function resolveRole(AuditLog $log): string
    {
        $properties = $this->properties($log);
        $role = trim((string) ($properties['role'] ?? ''));

        if ($role !== '') {
            return $role;
        }

        if ($log->subject !== null) {
            $subjectName = $this->stringValue($log->subject->name ?? null);
            $subjectSlug = $this->stringValue($log->subject->slug ?? null);

            if ($subjectName !== '') {
                return $subjectName;
            }

            if ($subjectSlug !== '') {
                return $subjectSlug;
            }
        }

        return '—';
    }

    private function resolveIpAddress(AuditLog $log): string
    {
        $ipAddress = trim((string) ($log->ip_address ?? ''));

        return $ipAddress !== '' ? $ipAddress : '—';
    }

    private function resolveCreatedAt(AuditLog $log): string
    {
        return $log->created_at !== null
            ? $log->created_at->format('d.m.Y H:i:s')
            : '—';
    }

    /**
     * @return array<int, string>
     */
    private function resolveDetails(AuditLog $log): array
    {
        $properties = $this->properties($log);
        $details = [];

        if (array_key_exists('is_active', $properties)) {
            $details[] = (string) __('audit::audit.status') . ': ' .
                ((bool) $properties['is_active']
                    ? (string) __('audit::audit.active')
                    : (string) __('audit::audit.inactive'));
        }

        $permission = trim((string) ($properties['permission'] ?? ''));
        if ($permission !== '') {
            $details[] = (string) __('audit::audit.permission') . ': ' . $permission;
        }

        $permissionIds = $properties['permission_ids'] ?? null;
        if (is_array($permissionIds) && $permissionIds !== []) {
            $details[] = (string) __('audit::audit.permission_ids') . ': ' .
                implode(', ', array_map(static fn (mixed $id): string => (string) $id, $permissionIds));
        }

        $themeGroup = trim((string) ($properties['group'] ?? ''));
        $themeName = trim((string) ($properties['theme'] ?? ''));

        if ($themeGroup !== '' && $themeName !== '') {
			$details[] = (string) __('audit::audit.theme_group') . ': ' . $themeGroup;
			$details[] = (string) __('audit::audit.theme_name') . ': ' . $themeName;
        }

        return $details;
    }

    /**
     * @return array<string, mixed>
     */
    private function properties(AuditLog $log): array
    {
        return is_array($log->properties) ? $log->properties : [];
    }

    private function stringValue(mixed $value): string
    {
        return is_string($value) ? trim($value) : '';
    }
}