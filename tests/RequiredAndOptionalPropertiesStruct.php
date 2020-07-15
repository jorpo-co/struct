<?php declare(strict_types=1);

namespace Jorpo\Struct;

class RequiredAndOptionalPropertiesStruct extends Struct
{
    /**
     * Required properties are typed and empty
     */
    protected string $required;

    /**
     * Optional properties are nullable and *must* be set to null
     * If not null, they are considered as required properties
     */
    protected ?string $optional = null;
}
