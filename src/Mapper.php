<?php
namespace Xenolope\Cartographer;

use Minime\Annotations\Cache\ArrayCache;
use Minime\Annotations\Interfaces\ReaderInterface;
use Minime\Annotations\Parser;
use Minime\Annotations\Reader;
use Xenolope\Cartographer\Exception\InvalidPropertyTypeException;
use Xenolope\Cartographer\Exception\InvalidSetterTypeException;
use Xenolope\Cartographer\Exception\JsonDecodingException;

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
        $decodedJson = json_decode($json, true);
        if (!$decodedJson) {
            throw new JsonDecodingException(json_last_error_msg());
        }

        return $this->map($decodedJson, $objectClass);
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

    private function getVarType(\ReflectionClass $reflectedClass, $key)
    {
        $property = $this->camelize($key);

        // Check if the class has a property with the same name as the JSON key
        if ($reflectedClass->hasProperty($property)) {
            $annotations = $this->annotationReader->getPropertyAnnotations($reflectedClass->getName(), $property);

            if ($annotations->has('var')) {
                return $annotations->get('var');
            }
        }

        throw new InvalidPropertyTypeException($reflectedClass->getName(), $property);
    }

    private function getTypedValue(\ReflectionClass $reflectedClass, $setter, $key, $value)
    {
        $type = $this->getVarType($reflectedClass, $key);

        // Check if the type is a simple type (string, int, bool, etc.)
        if ($this->isSimpleType($type)) {
            // @todo Not sure if this is the best way (see http://php.net/manual/en/function.settype.php)
            settype($value, $type);
            return $value;
        } elseif (substr($type, -2) === '[]') {
            $array = new \SplFixedArray(count($value));
            $arrayObjectType = $this->getType($reflectedClass, rtrim($type, '[]'), $setter);

            foreach ($value as $index => $arrayObject) {
                $array[$index] = $this->map($arrayObject, $arrayObjectType);
            }

            return $array;
        } else {
            return $this->map($value, $this->getType($reflectedClass, $type, $setter));
        }
    }

    private function getSetter($property)
    {
        return 'set' . $this->classify($property);
    }

    private function getType(\ReflectionClass $reflectedClass, $varType, $setter)
    {
        // Check if the type is namespaced, if not then create the full namespaced class and instantiate it
        if (0 !== strpos($varType, '\\')) {
            $varType = $reflectedClass->getNamespaceName() . '\\' . $varType;
        }

        // If the parsed class doesn't exist (class could be using a 'use'), look at the setter
        if (!class_exists($varType)) {
            $setterParameters = $reflectedClass->getMethod($setter)->getParameters();
            if (count($setterParameters) > 0 && $setterParameters[0]->getClass() !== null) {
                $varType = $setterParameters[0]->getClass()->getName();
            } else {
                throw new InvalidSetterTypeException($reflectedClass->getName(), $setter);
            }
        }

        return $varType;
    }

    private function camelize($value)
    {
        return lcfirst($this->classify($value));
    }

    private function classify($value)
    {
        return str_replace(' ', '', ucwords(strtr($value, '_-', '  ')));
    }

    private function isSimpleType($type)
    {
        return in_array($type, ['string', 'boolean', 'bool', 'integer', 'int', 'float', 'array', 'object']);
    }
}
