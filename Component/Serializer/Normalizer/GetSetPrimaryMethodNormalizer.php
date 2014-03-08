<?php

namespace thomasbeaujean\GetSetForeignNormalizerBundle\Component\Serializer\Normalizer;

use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\RuntimeException;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

/**
 * Converts between objects with getter and setter methods and arrays.
 *
 * @author Thomas Beaujean
 */
class GetSetPrimaryMethodNormalizer extends GetSetMethodNormalizer
{
    var $doctrine = null;

    /**
     * Constructor
     *
     * @param Doctrine $doctrine The doctrine service
     *
     * @throws \Exception
     *
     * @return nothing
     */
    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;

        if ($this->doctrine === null) {
            throw new \Exception('The class GetSetMethodForeignNormalizer needs the doctrine service in order to normalize, please give it to the constructor');
        }
    }

    /**
     * Convert an object using the getter of this one, and if it has some foreign relationship, we use also the id of the foreign objects
     *
     * @param unknown $object  The object to convert
     * @param string  $format  Not used here, keeped for compatibility
     * @param array   $context Not used here, keeped for compatibility
     *
     * @return multitype:multitype:multitype:mixed
     */
    public function normalize($object, $format = null, array $context = array())
    {
        $reflectionObject = new \ReflectionObject($object);
        $reflectionMethods = $reflectionObject->getMethods(\ReflectionMethod::IS_PUBLIC);

        $attributes = array();
        foreach ($reflectionMethods as $method) {
            if ($this->isGetMethod($method)) {
                $attributeName = lcfirst(substr($method->name, 3));//we sub the set or get

                if (in_array($attributeName, $this->ignoredAttributes)) {
                    continue;
                }

                $attributeValue = $method->invoke($object);

                if (array_key_exists($attributeName, $this->callbacks)) {
                    $attributeValue = call_user_func($this->callbacks[$attributeName], $attributeValue);
                }
                if (null !== $attributeValue && !is_scalar($attributeValue)) {
                    if (get_class($attributeValue) == 'Doctrine\ORM\PersistentCollection') {
                        //memorize the list of persistent collections
                        $attributeValues = $attributeValue;

                        $attributeValue = array();
                        foreach ($attributeValues as $obj) {
                            $attributeReflectionObject = new \ReflectionObject($obj);

                            $identifiers = $this->doctrine->getManager()->getMetadataFactory()->getMetadataFor($attributeReflectionObject->getName())->getIdentifier();
                            //the ids to add
                            $tempAttribute = array();

                            foreach ($identifiers as $identifier) {
                                $attribute = call_user_func(array($obj, 'get'.ucfirst($identifier)));
                                //the attribute is itself an object
                                if (is_object($attribute)) {
                                    //we look for the ids
                                    $attributeIdentifierReflectionObject = new \ReflectionObject($attribute);
                                    $attributeIdentifiers = $this->doctrine->getManager()->getMetadataFactory()->getMetadataFor($attributeIdentifierReflectionObject->getName())->getIdentifier();

                                    foreach ($attributeIdentifiers as $index => $attributeIdentifier) {
                                         $attributeIdentifierAttribute = call_user_func(array($attribute, 'get'.ucfirst($attributeIdentifier)));//@todo use reflection to know the identifier
                                         //we add each of the ids
                                         $tempAttribute[$identifier] = $attributeIdentifierAttribute;
                                    }
                                } else {
                                    //we memorise the array of ids
                                    $tempAttribute[$identifier] = $attribute;
                                }
                            }
                            //we add the id to the array of the attribute
                            $attributeValue[] = $tempAttribute;
                        }
                    } else {
                        $attributeValue = $attributeValue->getId();//@todo use reflection to know the identifier
                    }
                }
                $attributes[$attributeName] = $attributeValue;
            }
        }

        return $attributes;
    }

    /**
     * Checks if a method's name is get.* and can be called without parameters.
     *
     * @param \ReflectionMethod $method the method to check
     *
     * @return Boolean whether the method is a getter.
     */
    private function isGetMethod(\ReflectionMethod $method)
    {
        return (
            0 === strpos($method->name, 'get') &&
            3 < strlen($method->name) &&
            0 === $method->getNumberOfRequiredParameters()
        );
    }
}
