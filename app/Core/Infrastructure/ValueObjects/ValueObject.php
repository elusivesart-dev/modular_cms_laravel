<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\ValueObjects;

abstract class ValueObject
{
    final public function equals(ValueObject $object): bool
    {
        return $this == $object;
    }
}