<?php

namespace tbn\GetSetForeignNormalizerBundle\Converter;

/**
 *
 * @author Thomas BEAUJEAN
 */
class TimeConverter implements ConverterInterface
{
    /**
     *
     * @param type $rawValue
     *
     * @return string The converted value
     */
    public function convert($rawValue)
    {
        $convertedValue = $rawValue->format('H:i:s');

        return $convertedValue;
    }
}
