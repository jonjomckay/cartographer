<?php
namespace Xenolope\Cartographer;

use Xenolope\Cartographer\Entity\Contact;
use Xenolope\Cartographer\Entity\ContactInvalidVarButValidSetterType;
use Xenolope\Cartographer\Entity\ContactWithAddressArray;

class MapperTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Mapper
     */
    private $mapper;

    private static $json = '{"name":"Liz Lemon","address": {"street": "168 Riverside Drive","city": "New York"}}';

    public function setUp()
    {
        $this->mapper = new Mapper();
    }

    public function testMapString()
    {
        /** @var Contact $result */
        $result = $this->mapper->mapString(static::$json, 'Xenolope\Cartographer\Entity\Contact');

        $this->assertEquals('Liz Lemon', $result->getName());
        $this->assertInstanceOf('Xenolope\Cartographer\Entity\Address', $result->getAddress());
        $this->assertEquals('168 Riverside Drive', $result->getAddress()->getStreet());
        $this->assertEquals('New York', $result->getAddress()->getCity());
    }

    public function testMapStringWithInvalidPropertyType()
    {
        $this->setExpectedException('Xenolope\Cartographer\Exception\InvalidPropertyTypeException');

        $this->mapper->mapString(static::$json, 'Xenolope\Cartographer\Entity\ContactInvalidPropertyType');
    }

    public function testMapStringWithInvalidVarButValidSetterType()
    {
        /** @var ContactInvalidVarButValidSetterType $result */
        $result = $this->mapper->mapString(static::$json, 'Xenolope\Cartographer\Entity\ContactInvalidVarButValidSetterType');

        $this->assertEquals('Liz Lemon', $result->getName());
        $this->assertInstanceOf('Xenolope\Cartographer\Entity\Address', $result->getAddress());
        $this->assertEquals('168 Riverside Drive', $result->getAddress()->getStreet());
        $this->assertEquals('New York', $result->getAddress()->getCity());
    }

    public function testMapStringWithInvalidSetterType()
    {
        $this->setExpectedException('Xenolope\Cartographer\Exception\InvalidSetterTypeException');

        $this->mapper->mapString(static::$json, 'Xenolope\Cartographer\Entity\ContactInvalidSetterType');
    }

    public function testMapStringWithArrayProperty()
    {
        /** @var ContactWithAddressArray $result */
        $result = $this->mapper->mapString('{"name":"Liz Lemon","addresses": [{"street": "160 Riverside Drive","city": "New York"}, {"street": "168 Riverside Drive","city": "New York"}]}', 'Xenolope\Cartographer\Entity\ContactWithAddressArray');

        $this->assertEquals('Liz Lemon', $result->getName());
        $this->assertContainsOnlyInstancesOf('Xenolope\Cartographer\Entity\Address', $result->getAddresses());
        $this->assertCount(2, $result->getAddresses());
        $this->assertEquals('160 Riverside Drive', $result->getAddresses()[0]->getStreet());
        $this->assertEquals('New York', $result->getAddresses()[0]->getCity());
        $this->assertEquals('168 Riverside Drive', $result->getAddresses()[1]->getStreet());
        $this->assertEquals('New York', $result->getAddresses()[1]->getCity());
    }
}
