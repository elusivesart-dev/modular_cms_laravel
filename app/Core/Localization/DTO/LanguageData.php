<?php

declare(strict_types=1);

namespace App\Core\Localization\DTO;

final readonly class LanguageData
{
    public function __construct(
        public string $code,
        public string $name,
        public string $nativeName,
        public string $direction,
        public ?string $version,
        public ?string $installedPath,
        public bool $isActive,
        public bool $isSystem,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            code: (string) ($data['code'] ?? ''),
            name: (string) ($data['name'] ?? ''),
            nativeName: (string) ($data['native_name'] ?? ''),
            direction: (string) ($data['direction'] ?? 'ltr'),
            version: isset($data['version']) ? (string) $data['version'] : null,
            installedPath: isset($data['installed_path']) ? (string) $data['installed_path'] : null,
            isActive: (bool) ($data['is_active'] ?? true),
            isSystem: (bool) ($data['is_system'] ?? false),
        );
    }

    /**
     * @return array{
     *     code:string,
     *     name:string,
     *     native_name:string,
     *     direction:string,
     *     version:?string,
     *     installed_path:?string,
     *     is_active:bool,
     *     is_system:bool
     * }
     */
    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
            'native_name' => $this->nativeName,
            'direction' => $this->direction,
            'version' => $this->version,
            'installed_path' => $this->installedPath,
            'is_active' => $this->isActive,
            'is_system' => $this->isSystem,
        ];
    }
}