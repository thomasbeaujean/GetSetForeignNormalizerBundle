<?php

namespace tbn\GetSetForeignNormalizerBundle\Converter;

/**
 *
 * @author Thomas BEAUJEAN
 */
class DatetimeConverter extends AbstractDatetimeConverter
{
    /**
     * Get the format
     * @return string
     */
    protected function getFormat()
    {
        return 'Y-m-d H:i:s';
    }
}
