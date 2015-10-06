<?php

namespace tbn\GetSetForeignNormalizerBundle\Converter;

/**
 *
 * @author Thomas BEAUJEAN
 */
abstract class AbstractDatetimeConverter implements ConverterInterface
{
    /**
     *
     * @param type $rawValue
     *
     * @return string The converted value
     */
    public function convert($rawValue)
    {
        $convertedValue = $rawValue->format($this->getFormat());

        return $convertedValue;
    }

    /**
     *
     * @param string $rawValue
     * @return \DateTime
     */
    public function revert($rawValue)
    {
        $datetime = \DateTime::createFromFormat($this->getFormat(), $rawValue);

        return $datetime;
    }

    /**
     * @return string The format
     */
    protected abstract function getFormat();
}
