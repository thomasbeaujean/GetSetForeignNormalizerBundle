<?php

namespace tbn\GetSetForeignNormalizerBundle\Converter;

/**
 *
 * @author Thomas BEAUJEAN
 */
class BooleanConverter implements ConverterInterface
{
    /**
     * Nothing to do for boolean
     *
     * @param boolean $rawValue
     * @return boolean The same value
     */
    public function convert($rawValue)
    {
        return $rawValue;
    }
}
