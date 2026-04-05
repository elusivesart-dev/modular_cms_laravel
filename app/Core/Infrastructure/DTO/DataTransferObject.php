<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\DTO;

use Illuminate\Contracts\Support\Arrayable;
use InvalidArgumentException;
use ReflectionObject;
use ReflectionProperty;
use TypeError;

abstract class DataTransferObject implements Arrayable
{
    /**
     * @param array<string, mixed> $data
     */
    final public function __construct(array $data)
    {
        $this->hydrate($data);
        $this->validate();
    }

    /**
     * @param array<string, mixed> $data
     */
    private function hydrate(array $data): void
    {
        $knownProperties = $this->writableProperties();

        foreach ($data as $key => $value) {
            if (!array_key_exists($key, $knownProperties)) {
                throw new InvalidArgumentException(sprintf(
                    'Unknown DTO property [%s] for [%s].',
                    $key,
                    static::class
                ));
            }

            try {
                $this->{$key} = $value;
            } catch (TypeError $exception) {
                throw new InvalidArgumentException(sprintf(
                    'Invalid value for DTO property [%s] on [%s].',
                    $key,
                    static::class
                ), previous: $exception);
            }
        }
    }

    /**
     * Override in child DTOs for domain-level validation.
     */
    protected function validate(): void
    {
    }

    /**
     * @return array<string, ReflectionProperty>
     */
    private function writableProperties(): array
    {
        $reflection = new ReflectionObject($this);
        $properties = [];

        foreach ($reflection->getProperties() as $property) {
            if ($property->isStatic()) {
                continue;
            }

            $properties[$property->getName()] = $property;
        }

        return $properties;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $reflection = new ReflectionObject($this);
        $data = [];

        foreach ($reflection->getProperties() as $property) {
            if ($property->isStatic()) {
                continue;
            }

            $property->setAccessible(true);

            if (!$property->isInitialized($this)) {
                continue;
            }

            $data[$property->getName()] = $property->getValue($this);
        }

        return $data;
    }
}