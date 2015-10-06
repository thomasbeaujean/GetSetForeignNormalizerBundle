<?php

namespace tbn\GetSetForeignNormalizerBundle\Converter;

/**
 *
 * @author Thomas BEAUJEAN
 */
class TimeConverter extends AbstractDatetimeConverter
{
    /**
     * Get the format
     * @return string
     */
    protected function getFormat()
    {
        return 'H:i:s';
    }
}
