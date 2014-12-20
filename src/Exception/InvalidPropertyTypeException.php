<?php
namespace Xenolope\Cartographer\Exception;

class InvalidPropertyTypeException extends \Exception
{

    public function __construct($className, $property)
    {
        parent::__construct(sprintf('A valid type could not be found for the property %s::$%s', $className, $property));
    }
}
