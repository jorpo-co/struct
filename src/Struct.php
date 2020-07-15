<?php declare(strict_types=1);

namespace Jorpo\Struct;

use DomainException;
use Ds\Hashable;
use Ds\Pair;

abstract class Struct implements Hashable
{
    public function __construct(Pair ...$pairs)
    {
        foreach ($pairs as $pair) {
            if (property_exists($this, $pair->key)) {
                $this->{$pair->key} = $pair->value;
            }
        }

        $this->reportUnsetRequiredProperties();
    }

    public function __get(string $name)
    {
        return $this->{$name};
    }

    public function __set(string $name, $value)
    {
        return;
    }

    public function change(string $key, $value): self
    {
        $clone = clone $this;
        $clone->{$key} = $value;

        return $clone;
    }

    private function reportUnsetRequiredProperties(): void
    {
        $required = array_diff(
            array_keys(get_class_vars(static::class)),
            array_keys(get_object_vars($this))
        );

        if (!empty($required)) {
            throw new DomainException(sprintf(
                "Required parameters: '%s' are not set",
                implode(", ", $required)
            ));
        }
    }

    public function hash()
    {
        return serialize($this);
    }

    public function equals($obj): bool
    {
        return get_called_class() === get_class($this) && $this->hash() === $obj->hash();
    }
}
