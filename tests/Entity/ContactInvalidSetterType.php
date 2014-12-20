<?php
namespace Xenolope\Cartographer\Entity;

use Xenolope;

class ContactInvalidSetterType
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var Xenolope\Cartographer\Entity\Address
     */
    private $address;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param Address $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }
}
