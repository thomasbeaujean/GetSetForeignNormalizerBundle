<?php

namespace tbn\GetSetForeignNormalizerBundle\Converter;

/**
 *
 * @author Thomas BEAUJEAN
 */
class IntegerConverter implements ConverterInterface
{
    /**
     *
     * @param type $rawValue
     * @return integer The casted value
     */
    public function convert($rawValue)
    {
        $convertedValue = intval($rawValue);

        return $convertedValue;
    }
}
