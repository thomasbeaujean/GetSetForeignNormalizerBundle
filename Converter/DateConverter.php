<?php

namespace tbn\GetSetForeignNormalizerBundle\Converter;

/**
 * Description of IntegerConverter
 *
 * @author Thomas BEAUJEAN
 */
class DateConverter implements ConverterInterface
{
    /**
     *
     * @param type $rawValue
     *
     * @return string The converted value
     */
    public function convert($rawValue)
    {
        $convertedValue = $rawValue->format('Y-m-d');

        return $convertedValue;
    }
}
