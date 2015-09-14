<?php

namespace tbn\GetSetForeignNormalizerBundle\Component\Serializer\Normalizer;

use tbn\GetSetForeignNormalizerBundle\Component\Serializer\Normalizer\Traits;
use tbn\GetSetForeignNormalizerBundle\Converter\ConverterManager;

/**
 * Converts between objects with getter and setter methods and arrays.
 *
 * @author Thomas Beaujean
 * ref: get_set_foreign_normalizer.service.doctrine_entity_identifier_normalizer
 */
class DoctrineEntityIdentifierNormalizer
{
    use Traits\IsDoctrineEntityTrait;
    protected $doctrine = null;
    protected $converterManager = null;

    /**
     * Constructor
     *
     * @param Doctrine         $doctrine         The doctrine service
     * @param ConverterManager $converterManager
     *
     * @throws \Exception
     *
     * @return nothing
     */
    public function __construct($doctrine, ConverterManager $converterManager)
    {
        $this->doctrine = $doctrine;
        $this->converterManager = $converterManager;

        if ($this->doctrine === null) {
            throw new \Exception('The class GetSetMethodForeignNormalizer needs the doctrine service in order to normalize, please give it to the constructor');
        }
    }

    /**
     * Convert an object using the getter of this one, and if it has some foreign relationship, we use also the id of the foreign objects
     *
     * @param unknown $entity The data to convert
     *
     * @return multitype:multitype:multitype:mixed
     */
    public function normalize($entity)
    {
        $identifiers = $this->getIdentifiers(get_class($entity));

        $metadata = $this->getMetadata(get_class($entity));
        $fieldMappings = $metadata->fieldMappings;

        $identifierValues = array();
        foreach ($identifiers as $identifier) {
            $rawValue = $this->getObjectAttribute($entity, $identifier);

            //the identifiers might be a field or an entity
            if ($this->isDoctrineEntity($rawValue)) {
                //it is a doctrine entity
                $convertedValue = $this->normalize($rawValue);
            } else {
                //it is a field
                $fieldMapping = $fieldMappings[$identifier];
                $doctrineType = $fieldMapping['type'];
                $convertedValue = $this->converterManager->convert($doctrineType, $rawValue);
            }

            $identifierValues[$identifier] = $convertedValue;
        }

        $attributeValue = $identifierValues;
        unset($identifierValues);

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

    /**
     *
     * @param String $entityClass
     */
    protected function getIdentifiers($entityClass)
    {
        $meta = $this->getMetadata($entityClass);

        return $meta->identifier;
    }
}
