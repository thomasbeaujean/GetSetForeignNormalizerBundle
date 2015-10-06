<?php

namespace tbn\GetSetForeignNormalizerBundle\Converter;

/**
 *
 * @author Thomas BEAUJEAN
 */
class DefaultConverter implements ConverterInterface
{
    /**
     *
     * @param unknown $rawValue
     * @return unknown The same value
     */
    public function convert($rawValue)
    {
        return $rawValue;
    }

    /**
     * Revert the value
     * @param string $rawValue
     * @return string
     */
    public function revert($rawValue)
    {
        return $rawValue;
    }
}
