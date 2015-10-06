<?php

namespace tbn\GetSetForeignNormalizerBundle\Converter;

/**
 *
 * @author Thomas BEAUJEAN
 */
interface ConverterInterface
{
    /**
     *
     * @param type $rawValue
     */
    public function convert($rawValue);

    /**
     *
     * @param type $rawValue
     */
    public function revert($rawValue);
}
