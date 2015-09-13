<?php

namespace tbn\GetSetForeignNormalizerBundle\Converter;

/**
 * Description of IntegerConverter
 *
 * @author Thomas BEAUJEAN
 */
class ConverterManager
{
    /**
     *
     * @param type $doctrineType
     * @param type $value
     * @return string The converted value
     */
    public function convert($doctrineType, $value)
    {
        $converterMapping = [
            'time' => new TimeConverter(),
            'date' => new DateConverter(),
            'datetime' => new DatetimeConverter(),
        ];

        if (isset($converterMapping[$doctrineType])) {
            $converter = $converterMapping[$doctrineType];
        } else {
            $converter = new DefaultConverter();
        }

        $convertedValue = $converter->convert($value);

        return $convertedValue;
    }
}
