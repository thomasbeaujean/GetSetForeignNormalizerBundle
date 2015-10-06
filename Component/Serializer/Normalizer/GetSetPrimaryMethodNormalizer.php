<?php

namespace tbn\GetSetForeignNormalizerBundle\Component\Serializer\Normalizer;

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use tbn\GetSetForeignNormalizerBundle\Converter\ConverterManager;
use tbn\GetSetForeignNormalizerBundle\Component\Serializer\Normalizer\Traits;

/**
 * Converts between objects with getter and setter methods and arrays.
 *
 * @author Thomas Beaujean
 */
class GetSetPrimaryMethodNormalizer
{
    use Traits\IsDoctrineEntityTrait;
    protected $doctrine = null;
    protected $deepNormalization = false;
    protected $decamelize = false;
    protected $ignoredAttributes = array();
    protected $normalizedEntities = array();
    protected $converterManager = null;
    protected $doctrineEntityIdentifierNormalizer = null;

    /**
     * Constructor
     *
     * @param Doctrine                           $doctrine                           The doctrine service
     * @param ConverterManager                   $converterManager
     * @param DoctrineEntityIdentifierNormalizer $doctrineEntityIdentifierNormalizer
     * @throws \Exception
     *
     * @return nothing
     */
    public function __construct($doctrine, ConverterManager $converterManager, DoctrineEntityIdentifierNormalizer $doctrineEntityIdentifierNormalizer)
    {
        $this->doctrine = $doctrine;
        $this->converterManager = $converterManager;
        $this->doctrineEntityIdentifierNormalizer = $doctrineEntityIdentifierNormalizer;

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
     *
     * @param boolean $decamelize
     */
    public function setDecamelize($decamelize)
    {
        $this->decamelize = $decamelize;
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
     * Convert an object using the getter of this one, and if it has some foreign relationship, we use also the id of the foreign objects
     *
     * @param unknown $data The data to convert
     *
     * @return multitype:multitype:multitype:mixed
     */
    public function normalize($data)
    {
        if ($data instanceof \Traversable || is_array($data)) {
            $normalized = $this->normalizeArray($data);
        } else {
            $normalized = $this->normalizeObject($data);
        }

        return $normalized;
    }

    /**
     * Convert an object using the getter of this one, and if it has some foreign relationship, we use also the id of the foreign objects
     *
     * @param unknown $data    The data to convert
     * @param string  $format  Not used here, keeped for compatibility
     * @param array   $context Not used here, keeped for compatibility
     *
     * @return multitype:multitype:multitype:mixed
     */
    protected function normalizeArray($data, $format = null, array $context = array())
    {
        $normalized = array();

        //parse all data
        foreach ($data as $index => $row) {
            $normalizedData = $this->normalize($row, $format, $context);

            //reset the normalized entities to have the same list for all entities
            $this->normalizedEntities = [];

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
     * Is the attribute a doctrine collection
     *
     * @param unknown $attribute
     * @return boolean
     */
    protected function isDoctrineCollection($attribute)
    {
        $isDoctrineCollection = false;

        if (is_object($attribute) && get_class($attribute) === 'Doctrine\ORM\PersistentCollection') {
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
                $attributeValue[] = $this->doctrineEntityIdentifierNormalizer->normalize($obj);
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
        $deepNormalization = $this->deepNormalization;

        //once the entities has been normalized, it wont be normalized twice to avoid infinite loops
        if (in_array($entity, $this->normalizedEntities)) {
            $deepNormalization = false;
        } else {
            $this->normalizedEntities[] = $entity;
        }

        if ($deepNormalization) {
            //the foreign entities are also normalized using the same conditions (think to ignored properties)
            $attributeValue = $this->normalize($entity);
        } else {
            $identifierValues = $this->doctrineEntityIdentifierNormalizer->normalize($entity);

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
            $attributeValue = call_user_func(array($object, $methodName));
        } else {
            throw new \Exception('The the entity does not have a '.$methodName.' method');
        }

        return $attributeValue;
    }

    /**
     * Convert an object using the getter of this one, and if it has some foreign relationship, we use also the id of the foreign objects
     *
     * @param unknown $object  The object to convert
     *
     * @return multitype:multitype:multitype:mixed
     */
    protected function normalizeObject($object)
    {
        $methods = $this->getObjectMethods($object);

        $isDoctrineEntity = $this->isDoctrineEntity($object);
        if ($isDoctrineEntity) {
            $metadata = $this->getMetadata(get_class($object));
        }

        $attributes = array();

        foreach ($methods as $method) {
            if ($this->isGetMethod($method)) {
                $attributeName = $this->getMethodAttributeName($method);

                //is the attribute allowed
                if (in_array($attributeName, $this->ignoredAttributes)) {
                    continue;
                }

                if ($isDoctrineEntity) {
                    $attributeValue = $this->getAttributeValueByMethod($object, $method, $metadata);
                } else {
                    $rawValue = $this->getAttributeValue($object, $method);
                    if (is_object($rawValue)) {
                        $attributeValue = $this->normalizeObject($rawValue);
                    } elseif (is_array($rawValue)) {
                        $attributeValue = $this->normalizeArray($rawValue);
                    } else {
                        $attributeValue = $rawValue;
                    }
                }

                if ($this->decamelize) {
                    $attributeName = $this->decamelize($attributeName);
                }

                $attributes[$attributeName] = $attributeValue;
            }
        }

        return $attributes;
    }

    /**
     *
     * @param type $object
     * @param type $method
     * @param type $metadata
     * @return type
     */
    protected function getAttributeValueByMethod($object, $method, $metadata)
    {
        $fieldMappings = $metadata->fieldMappings;

        $associationMappings = $metadata->associationMappings;

        $attributeName = $this->getMethodAttributeName($method);

        $attributeValue = $this->getAttributeValue($object, $method);

        if ($attributeValue !== null) {
            //a property
            if (isset($fieldMappings[$attributeName])) {
                $fieldMapping = $fieldMappings[$attributeName];
                $doctrineType = $fieldMapping['type'];
                $attributeValue = $this->converterManager->convert($doctrineType, $attributeValue);
            } elseif (isset($associationMappings[$attributeName])) {
                $associationMapping = $associationMappings[$attributeName];
                $associationType = $associationMapping['type'];
                $attributeValue = $this->convertAssociationType($associationType, $attributeValue);
            } else {
                if (is_array($attributeValue)) { //the array must be tested before the is doctrine
                    $attributeValue = $this->normalize($attributeValue);
                } elseif ($attributeValue instanceof \DateTime) {
                    $converter = new Converter\DatetimeConverter();
                    $attributeValue = $converter->convert($attributeValue);
                }
            }
        }

        return $attributeValue;
    }

    /**
     *
     * @param string  $associationType
     * @param unknown $value
     *
     * @return unknown
     */
    protected function convertAssociationType($associationType, $value)
    {
        switch ($associationType) {
            case ClassMetadataInfo::ONE_TO_ONE:
            case ClassMetadataInfo::MANY_TO_ONE:
            case ClassMetadataInfo::TO_ONE:
                $convertedValue = $this->normalizeDoctrineEntity($value);
                break;
            case ClassMetadataInfo::ONE_TO_MANY:
            case ClassMetadataInfo::MANY_TO_MANY:
            case ClassMetadataInfo::TO_MANY:
                $convertedValue = $this->normalizeDoctrineCollection($value);
                break;
        }

        return $convertedValue;
    }

    /**
     * Decamelize a word
     *
     * @param string $word
     * @return string Decamelized string
     */
    protected function decamelize($word)
    {
        return preg_replace_callback(
            '/(^|[a-z])([A-Z])/', function ($matches) {
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
    protected function isGetMethod(\ReflectionMethod $method)
    {
        return (
            0 === strpos($method->name, 'get') &&
            3 < strlen($method->name) &&
            0 === $method->getNumberOfRequiredParameters()
        );
    }

    /**
     *
     * @param String $entityClass
     *
     * @return unknown
     */
    protected function getMetadata($entityClass)
    {
        $em = $this->doctrine->getManager();
        $meta = $em->getMetadataFactory()->getMetadataFor($entityClass);

        return $meta;
    }
}
