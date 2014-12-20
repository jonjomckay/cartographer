<?php
namespace Xenolope\Cartographer\Entity;

use Xenolope;

class ContactWithAddressArray
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var Address[]
     */
    private $addresses;

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
     * @return Address[]
     */
    public function getAddresses()
    {
        return $this->addresses;
    }

    /**
     * @param Address[] $addresses
     */
    public function setAddresses($addresses)
    {
        $this->addresses = $addresses;
    }
}
