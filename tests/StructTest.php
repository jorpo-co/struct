<?php declare(strict_types=1);

namespace Jorpo\Struct;

use DomainException;
use Ds\Pair;
use PHPUnit\Framework\TestCase;

class StructTest extends TestCase
{
    public function testThatStructCanBeEmpty()
    {
        $struct = new DummyStruct;
        $this->assertInstanceOf(Struct::class, $struct);
    }

    public function testThatStructRespectsDefinedProperties()
    {
        $struct = new BasicPropertiesStruct(
            new Pair('mushroom', 'badger')
        );
        $this->assertSame('badger', $struct->mushroom);
    }

    public function testThatStructPropertiesCanHaveDefaultValues()
    {
        $struct = new DefaultValuesStruct;
        $this->assertSame('badger', $struct->mushroom);
    }

    public function testThatStructUnderstandsRequiredAndOptionalProperties()
    {
        $struct = new RequiredAndOptionalPropertiesStruct(
            new Pair('required', 'badger')
        );
        $this->assertSame('badger', $struct->required);
        $this->assertNull($struct->optional);
    }

    public function testThatNotSettingRequiredPropertyIsNotAllowed()
    {
        $this->expectException(DomainException::class);
        new RequiredAndOptionalPropertiesStruct;
    }

    public function testThatStructIsImmutableOnceCreated()
    {
        $struct = new BasicPropertiesStruct(
            new Pair('mushroom', 'badger')
        );
        $struct->mushroom = 'snaaake!';

        $this->assertSame('badger', $struct->mushroom);
    }

    public function testThatChangingStructPropertyReturnsNewStruct()
    {
        $struct = new RequiredAndOptionalPropertiesStruct(
            new Pair('required', 'badger'),
            new Pair('optional', 'mushroom')
        );
        $changed = $struct->change('required', 'snaaake!');

        $this->assertNotSame($struct, $changed);
        $this->assertSame('snaaake!', $changed->required);
        $this->assertSame('mushroom', $changed->optional);
    }

    public function testThatOnlyDefinedPropertiesCanBeSetInStruct()
    {
        $struct = new RequiredAndOptionalPropertiesStruct(
            new Pair('required', 'badger'),
            new Pair('mushroom', 'badger')
        );

        $this->assertSame('badger', $struct->required);
        $this->assertNull($struct->mushroom);
    }

    public function testThatStructHasEqualityCheck()
    {
        $structOne = new RequiredAndOptionalPropertiesStruct(
            new Pair('required', 'badger'),
            new Pair('optional', 'mushroom')
        );
        $structTwo = new RequiredAndOptionalPropertiesStruct(
            new Pair('required', 'badger'),
            new Pair('optional', 'mushroom')
        );
        $structThree = new RequiredAndOptionalPropertiesStruct(
            new Pair('required', 'badger'),
            new Pair('optional', 'snaaake!')
        );

        $this->assertTrue($structOne->equals($structTwo));
        $this->assertFalse($structOne->equals($structThree));
    }

    public function testThatStructCanBeBuiltFromExistingArray()
    {
        $struct = RequiredAndOptionalPropertiesStruct::fromArray([
            'required' => 'badger',
            'optional' => 'mushroom',
        ]);

        $this->assertSame('badger', $struct->required);
        $this->assertSame('mushroom', $struct->optional);
    }
}
