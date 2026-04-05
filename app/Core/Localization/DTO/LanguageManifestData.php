<?php

declare(strict_types=1);

namespace App\Core\Localization\DTO;

final readonly class LanguageManifestData
{
    public function __construct(
        public string $code,
        public string $name,
        public string $nativeName,
        public string $direction,
        public ?string $version,
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
        );
    }

    /**
     * @return array{
     *     code:string,
     *     name:string,
     *     native_name:string,
     *     direction:string,
     *     version:?string
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
        ];
    }
}