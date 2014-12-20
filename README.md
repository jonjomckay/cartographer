Cartographer
============

[![Build Status](https://travis-ci.org/jonjomckay/cartographer.svg)](https://travis-ci.org/jonjomckay/cartographer)

Cartographer is a super-simple library for deserializing JSON into POPOs, similar to [FasterXML's `jackson-databind` package](https://github.com/FasterXML/jackson-databind) for Java.

## Installation

The library can be installed with Composer, by including the following in your `composer.json`:

```json
{
    "require": {
        "xenolope/cartographer": "~0.5"
    }
}
```

## Usage

### POPOs

Your POPOs must have a property and corresponding setter, with either the property having a `@var ClassName` docblock, or the setter having a type hint:

```php
class Contact
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var Address
     */
    private $address;

    /**
     * Note, this property doesn't have a @var docblock, but the corresponding setter
     * below *does* have a type hint
     */
    private $secondaryAddress;

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param Address $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @param Address $secondaryAddress
     */
    public function setAddress(Address $secondaryAddress)
    {
        $this->secondaryAddress = $secondaryAddress;
    }
}
```

### Mapping

```php
// Create a new instance of the Mapper
$mapper = new \Xenolope\Cartographer\Mapper();

// Map a JSON string to a POPO

// PHP 5.4
$object = $mapper->mapString(
    '{"name":"Liz Lemon","address":{"street":"168 Riverside Dr.","city":"New York"}}',
    'Vendor\Package\Entity\Contact'
);

// PHP >=5.5
$object = $mapper->mapString(
    '{"name":"Liz Lemon","address":{"street":"168 Riverside Dr.","city":"New York"}}',
    Contact::class
);

// Map an already decoded (to array) JSON document to a POPO

// This might happen automatically in your Request class, for example
$jsonDocument = json_decode(
    '{"name":"Liz Lemon","address":{"street":"168 Riverside Dr.","city":"New York"}}',
    true
);

// PHP 5.4
$object = $mapper->map($jsonDocument, 'Vendor\Package\Entity\Contact');

// PHP >= 5.5
$object = $mapper->map(
    '{"name":"Liz Lemon","address":{"street":"168 Riverside Dr.","city":"New York"}}',
    Contact::class
);
```

## Thanks

This library was inspired by:

* [`fasterxml/jackson-databind`](https://github.com/FasterXML/jackson-databind) for Java
* [`netresearch/jsonmapper`](https://github.com/netresearch/jsonmapper) for PHP

## License

Cartographer is released under the MIT License; please see [LICENSE](LICENSE) for more information.