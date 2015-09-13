<?php

namespace tbn\GetSetForeignNormalizerBundle\Converter;

/**
 *
 * @author Thomas BEAUJEAN
 */
class DatetimeConverter implements ConverterInterface
{
    /**
     *
     * @param type $rawValue
     *
     * @return string The converted value
     */
    public function convert($rawValue)
    {
        $convertedValue = $rawValue->format('Y-m-d H:i:s');

        return $convertedValue;
    }
}
