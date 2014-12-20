<?php
namespace Xenolope\Cartographer;

use Xenolope\Cartographer\Entity\Contact;

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

        /** @var Contact $result */
        $this->mapper->mapString(static::$json, 'Xenolope\Cartographer\Entity\ContactInvalidPropertyType');
    }

    public function testMapStringWithInvalidVarButValidSetterType()
    {
        /** @var Contact $result */
        $result = $this->mapper->mapString(static::$json, 'Xenolope\Cartographer\Entity\ContactInvalidVarButValidSetterType');

        $this->assertEquals('Liz Lemon', $result->getName());
        $this->assertInstanceOf('Xenolope\Cartographer\Entity\Address', $result->getAddress());
        $this->assertEquals('168 Riverside Drive', $result->getAddress()->getStreet());
        $this->assertEquals('New York', $result->getAddress()->getCity());
    }

    public function testMapStringWithInvalidSetterType()
    {
        $this->setExpectedException('Xenolope\Cartographer\Exception\InvalidSetterTypeException');

        /** @var Contact $result */
        $this->mapper->mapString(static::$json, 'Xenolope\Cartographer\Entity\ContactInvalidSetterType');
    }
}
