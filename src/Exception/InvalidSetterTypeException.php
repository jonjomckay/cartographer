<?php
namespace Xenolope\Cartographer\Exception;

class InvalidSetterTypeException extends \Exception
{

    public function __construct($className, $setter)
    {
        parent::__construct(sprintf('A valid type could not be found for the setter %s::%s()', $className, $setter));
    }
}
