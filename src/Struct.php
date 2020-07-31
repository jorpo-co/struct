<?php declare(strict_types=1);

namespace Jorpo\Struct;

use DomainException;
use Ds\Hashable;
use Ds\Pair;

abstract class Struct implements Hashable
{
    public static function fromArray(array $properties): self
    {
        foreach ($properties as $key => $value) {
            $properties[] = new Pair($key, $value);
            unset($properties[$key]);
        }

        return new static(...$properties);
    }

    public function __construct(Pair ...$pairs)
    {
        foreach ($pairs as $key => $pair) {
            if (property_exists($this, $pair->key)) {
                $this->{$pair->key} = $pair->value;
                unset($pairs[$key]);
            }
        }

        $this->reportUnsetRequiredProperties();
    }

    public function __get(string $name)
    {
        return $this->{$name} ?? null;
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

    /**
     * @return string
     */
    public function hash()
    {
        return serialize($this);
    }

    /**
     * @param Hashable $obj
     */
    public function equals($obj): bool
    {
        return get_called_class() === get_class($this) && $this->hash() === $obj->hash();
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }

    /**
     * @throws DomainException
     */
    private function reportUnsetRequiredProperties(): void
    {
        $required = array_diff(
            array_keys(get_class_vars(static::class)),
            array_keys(get_object_vars($this))
        );

        if (!empty($required)) {
            throw new DomainException(sprintf(
                "Required parameters: '%s' are not set.",
                implode(", ", $required)
            ));
        }
    }
}
