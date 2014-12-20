<?php
namespace Xenolope\Cartographer;

use Minime\Annotations\Cache\ArrayCache;
use Minime\Annotations\Interfaces\ReaderInterface;
use Minime\Annotations\Parser;
use Minime\Annotations\Reader;
use Xenolope\Cartographer\Exception\InvalidPropertyTypeException;
use Xenolope\Cartographer\Exception\InvalidSetterTypeException;

class Mapper
{

    /**
     * @var ReaderInterface
     */
    private $annotationReader;

    public function __construct(ReaderInterface $annotationReader = null)
    {
        $this->annotationReader = $annotationReader ?: new Reader(new Parser(), new ArrayCache());
    }

    public function mapString($json, $objectClass)
    {
        return $this->map(json_decode($json, true), $objectClass);
    }

    public function map(array $json, $objectClass)
    {
        $reflectedClass = new \ReflectionClass($objectClass);
        $object = new $objectClass;

        foreach ($json as $key => $value) {
            $setter = $this->getSetter($key);

            // Check if the class has an appropriate setter method
            if (method_exists($object, $setter)) {
                $typedValue = $this->getTypedValue($reflectedClass, $setter, $key, $value);
                $object->$setter($typedValue);
                continue;
            }
        }

        return $object;
    }

    private function getType(\ReflectionClass $reflectedClass, $key)
    {
        // Check if the class has a property with the same name as the JSON key
        if ($reflectedClass->hasProperty($key)) {
            $annotations = $this->annotationReader->getPropertyAnnotations($reflectedClass->getName(), $key);

            if ($annotations->has('var')) {
                return $annotations->get('var');
            }
        }

        throw new InvalidPropertyTypeException($reflectedClass->getName(), $key);
    }

    private function getTypedValue(\ReflectionClass $reflectedClass, $setter, $key, $value)
    {
        $type = $this->getType($reflectedClass, $key);

        // Check if the type is a simple type (string, int, bool, etc.)
        if ($this->isSimpleType($type)) {
            // @todo Not sure if this is the best way (see http://php.net/manual/en/function.settype.php)
            settype($value, $type);
            return $value;
        } else {
            // Check if the type is namespaced, if not then create the full namespaced class and instantiate it
            if (0 !== strpos($type, '\\')) {
                $type = $reflectedClass->getNamespaceName() . '\\' . $type;
            }

            // If the parsed class doesn't exist (class could be using a 'use'), look at the setter
            if (!class_exists($type)) {
                $setterParameters = $reflectedClass->getMethod($setter)->getParameters();
                if (count($setterParameters) > 0 && $setterParameters[0]->getClass() !== null) {
                    $type = $setterParameters[0]->getClass()->getName();
                } else {
                    throw new InvalidSetterTypeException($reflectedClass->getName(), $setter);
                }
            }

            return $this->map($value, $type);
        }
    }

    private function getSetter($property)
    {
        return 'set' . str_replace(' ', '', ucwords(strtr($property, '_-', '  ')));
    }

    private function isSimpleType($type)
    {
        return in_array($type, ['string', 'boolean', 'bool', 'integer', 'int', 'float', 'array', 'object']);
    }
}
