<?php

namespace tbn\GetSetForeignNormalizerBundle\Component\Serializer\Normalizer;

use tbn\GetSetForeignNormalizerBundle\Converter\ConverterManager;

/**
 * This services create a new instance of GetSetPrimaryMethodNormalizer
 * @author Thomas Beaujean
 */
class GetterMethodNormalizerFactory
{
    /**
     * Constructor
     *
     * @param Doctrine                           $doctrine                           The doctrine service
     * @param ConverterManager                   $converterManager
     * @param DoctrineEntityIdentifierNormalizer $doctrineEntityIdentifierNormalizer
     *
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
     *
     * @param type    $data
     * @param boolean $deepNormalization
     * @param boolean $decamelize
     * @param array   $ignoredAttributes
     * @return type
     */
    public function normalize($data, $deepNormalization = false, $decamelize = false, $ignoredAttributes = [])
    {
        $normalizer = new GetSetPrimaryMethodNormalizer($this->doctrine, $this->converterManager, $this->doctrineEntityIdentifierNormalizer);
        $normalizer->setDeepNormalization($deepNormalization);
        $normalizer->setDecamelize($decamelize);
        $normalizer->setIgnoredAttributes($ignoredAttributes);

        return $normalizer->normalize($data);
    }
}
