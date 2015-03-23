<?php

namespace tbn\GetSetForeignNormalizerBundle\Component\Serializer\Normalizer;

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
    var $deepNormalization = false;
    var $watchDog = 0;//avoid infinite loop
    var $decamelize = false;

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
     * Activate or not the deep normalization (the foreign entities are also normalized)
     *
     * @param boolean $deep
     */
    public function setDeepNormalization($deep)
    {
        $this->deepNormalization = $deep;
    }

    /**
     * Set the watchdog
     *
     * @param integer $watchDog
     */
    public function setWatchDog($watchDog)
    {
        $this->watchDog = $watchDog;
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
    public function normalize($data, $format = null, array $context = array())
    {
        if ($data instanceof \Traversable || is_array($data)) {
            $normalized = $this->normalizeArray($data, $format, $context);
        } else {
            $normalized = $this->normalizeObject($data, $format, $context);
        }

        return $normalized;
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
    public function normalizeArray($data, $format = null, array $context = array())
    {
        $normalized = array();

        //parse all data
        foreach ($data as $index => $row) {
            $normalizedData =$this->normalizeObject($row, $format, $context);
            $normalized[$index] = $normalizedData;
        }

        return $normalized;
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
    public function normalizeObject($object, $format = null, array $context = array())
    {
        //check the watchdog
        $this->checkWatchDog();

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

                if (null !== $attributeValue &&
                    !is_scalar($attributeValue) &&
                    !is_array($attributeValue) &&
                    (get_class($attributeValue) !== 'DateTime')
                ) {
                    if (get_class($attributeValue) == 'Doctrine\ORM\PersistentCollection') {
                        //memorize the list of persistent collections
                        $attributeValues = $attributeValue;

                        $attributeValue = array();
                        foreach ($attributeValues as $obj) {
                            if ($this->deepNormalization) {
                                //the foreign entities are also normalized using the same conditions (think to ignored properties)
                                $tempAttribute = $this->normalize($obj);
                                $attributeValue[] = $tempAttribute;
                            } else {
                                //it is a simple normalization, we just look for the identifiers
                                $attributeReflectionObject = new \ReflectionObject($obj);

                                $identifiers = $this->doctrine->getManager()->getMetadataFactory()->getMetadataFor($attributeReflectionObject->getName())->getIdentifier();
                                //the ids to add
                                $tempAttribute = array();

                                //we look for the multiple identifiers
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
                        }
                    } else {
                        //@todo use reflection to know the identifier
                        if (method_exists($attributeValue, 'getId')) {
                            $attributeValue = $attributeValue->getId();
                        }
                    }
                }

                //if the attribute is an array
                if (is_array($attributeValue)) {
                    $tempAttributeValue = array();

                    //we have to parse the content
                    foreach ($attributeValue as $tempValue) {
                        //to also normalize the objects
                        if (is_object($tempValue)) {
                            if ($this->deepNormalization) {
                                $tempAttribute = $this->normalize($tempValue);
                            } else {
                                $tempAttribute = $tempValue->getId();
                            }
                        } else {
                            $tempAttribute = $tempValue;
                        }

                        $tempAttributeValue[] = $tempAttribute;
                    }

                    //update the attribute value with the normalized array
                    $attributeValue = $tempAttributeValue;
                }

                //decamelize if requested the attribute name
                if ($this->decamelize === true) {
                    $attributeName = $this->decamelize($attributeName);
                }

                $attributes[$attributeName] = $attributeValue;

            }
        }

        return $attributes;
    }

    /**
     * Decamelize a word
     *
     * @param string $word
     * @return string Decamelized string
     */
    function decamelize($word)
    {
        return preg_replace_callback(
            '/(^|[a-z])([A-Z])/',function ($matches) {
            return strtolower($matches[1].'_').strtolower($matches[2]);},
            $word
        );
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

    /**
     * Check if the watchdog was raised
     *
     * @throws \Exception
     */
    private function checkWatchDog()
    {
        if ($this->watchDog >= 500) {
            throw new \Exception('The watchdog of '.$this->watchDog.' has been reached. There might be an infinite loop');
        }
        $this->watchDog++;
    }

    /**
     *
     * @param boolean $decamelize
     */
    public function setDecamelize($decamelize)
    {
        $this->decamelize = $decamelize;
    }
}
