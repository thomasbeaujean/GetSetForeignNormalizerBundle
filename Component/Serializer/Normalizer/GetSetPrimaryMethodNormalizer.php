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
    var $watchDogLimit = 500;
    var $decamelize = false;
    var $ignoredAttributes = array();

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
     * Ignore some attributes
     *
     * @param array $ignoredAttributes
     */
    public function setIgnoredAttributes(array $ignoredAttributes)
    {
        $this->ignoredAttributes = $ignoredAttributes;
    }

    /**
     * Set the watchdog limit
     *
     * @param integer $watchDogLimit
     */
    public function setWatchDogLimit($watchDogLimit)
    {
        $this->watchDogLimit = $watchDogLimit;
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
            $normalizedData = $this->normalizeObject($row, $format, $context);
            $normalized[$index] = $normalizedData;
        }

        return $normalized;
    }

    /**
     * Get the object method
     *
     * @param unknown $object
     * @return array
     */
    protected function getObjectMethods($object)
    {
        $reflectionObject = new \ReflectionObject($object);
        $reflectionMethods = $reflectionObject->getMethods(\ReflectionMethod::IS_PUBLIC);

        return $reflectionMethods;
    }

    /**
     * Get the name of the attribute linked to a get method
     *
     * @param string $method
     * @return string
     */
    protected function getMethodAttributeName($method)
    {
        $attributeName = lcfirst(substr($method->name, 3));//we sub the set or get

        return $attributeName;
    }

    /**
     *
     * @param Object $object
     * @param string $method
     * @return unknown
     */
    protected function getAttributeValue($object, $method)
    {
        $attributeValue = $method->invoke($object);

        return $attributeValue;
    }

    /**
     * Is the attributeValue a doctrine entity or a doctrine collection
     *
     * @param $attributeValue
     * @return boolean
     */
    protected function isDoctrineEntity($attributeValue)
    {
        $isDoctrineEntity = false;

        if (null !== $attributeValue &&
            !is_scalar($attributeValue) &&
            !is_array($attributeValue) &&
            (get_class($attributeValue) !== 'DateTime')
        ) {
            $isDoctrineEntity = true;
        }

        return $isDoctrineEntity;
    }

    /**
     * Is the attribute a doctrine collection
     *
     * @param unknown $attribute
     * @return boolean
     */
    protected function isDoctrineCollection($attribute)
    {
        $isDoctrineCollection = false;

        if (get_class($attribute) === 'Doctrine\ORM\PersistentCollection') {
            $isDoctrineCollection = true;
        }

        return $isDoctrineCollection;
    }

    /**
     *
     * @param unknown $collection
     */
    protected function normalizeDoctrineCollection($collection)
    {
        $attributeValue = array();

        foreach ($collection as $obj) {
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

        return $attributeValue;
    }

    /**
     *
     * @param Object $entity
     * @throws \Exception
     * @return multitype:\tbn\GetSetForeignNormalizerBundle\Component\Serializer\Normalizer\multitype:multitype:mixed
     */
    protected function normalizeDoctrineEntity($entity)
    {
        if ($this->deepNormalization) {
            //the foreign entities are also normalized using the same conditions (think to ignored properties)
            $attributeValue = $this->normalize($entity);
        } else {
            $identifiers = $this->getIdentifiers(get_class($entity));

            $identifierValues = array();
            foreach ($identifiers as $identifier) {
                $identifierValues[$identifier] = $this->getObjectAttribute($entity, $identifier);
            }

            $attributeValue = $identifierValues;
            unset($identifierValues);
        }

        return $attributeValue;
    }

    /**
     *
     * @param string $object
     * @param string $attributeName
     *
     * @throws \Exception The object does not have the expected method
     */
    protected function getObjectAttribute($object, $attributeName)
    {
        $methodName = 'get'.ucfirst($attributeName);

        if (method_exists($object, $methodName)) {
            $attributeValue = call_user_func( array($object, $methodName));
        } else {
            throw new \Exception('The the entity does not have a '.$methodName.' method');
        }

        return $attributeValue;
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

        $methods = $this->getObjectMethods($object);

        $attributes = array();

        foreach ($methods as $method) {

            if ($this->isGetMethod($method)) {

                $attributeName = $this->getMethodAttributeName($method);

                //is the attribute allowed
                if (in_array($attributeName, $this->ignoredAttributes)) {
                    continue;
                }

                $attributeValue = $this->getAttributeValue($object, $method);

                // $attributeValue can be an array, a doctrine collection, an int , datetime, a string
                if ($this->isDoctrineEntity($attributeValue)) {
                    if ($this->isDoctrineCollection($attributeValue)) {
                        //memorize the list of persistent collections
                        $attributeValue = $this->normalizeDoctrineCollection($attributeValue);
                    } else {
                        $attributeValue = $this->normalizeDoctrineEntity($attributeValue);
                    }
                } else if (is_array($attributeValue)) {
                    $attributeValue = $this->normalize($attributeValue);
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
        if ($this->watchDog >= $this->watchDogLimit) {
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

    /**
     *
     * @param String $entityClass
     * @return unknown
     */
    protected function getMetadata($entityClass)
    {
        $em = $this->doctrine->getEntityManager();
        $meta = $em->getMetadataFactory()->getMetadataFor($entityClass);

        return $meta;
    }

    /**
     *
     * @param String $itemNamespace
     */
    protected function getIdentifiers($entityClass)
    {
        $meta = $this->getMetadata($entityClass);

        return $meta->identifier;
    }
}
