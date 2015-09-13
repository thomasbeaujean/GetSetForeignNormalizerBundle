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
     * @param Doctrine         $doctrine         The doctrine service
     * @param int              $watchDogLimit    The watchdog limit
     * @param ConverterManager $converterManager
     *
     * @throws \Exception
     *
     * @return nothing
     */
    public function __construct($doctrine, $watchDogLimit, ConverterManager $converterManager, DoctrineEntityIdentifierNormalizer $doctrineEntityIdentifierNormalizer)
    {
        $this->doctrine = $doctrine;
        $this->watchDogLimit = $watchDogLimit;
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
     * @return type
     */
    public function normalize($data, $deepNormalization = false)
    {
        $normalizer = new GetSetPrimaryMethodNormalizer($this->doctrine, $this->watchDogLimit, $this->converterManager, $this->doctrineEntityIdentifierNormalizer);
        $normalizer->setDeepNormalization($deepNormalization);

        return $normalizer->normalize($data);
    }
}
