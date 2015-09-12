<?php

namespace tbn\GetSetForeignNormalizerBundle\Tests;

use tbn\GetSetForeignNormalizerBundle\Tests\PHPUnitKernelAware;

/**
 *
 * @author Thomas BEAUJEAN
 *
 */
class ArrayTest extends PHPUnitKernelAware
{
    /**
     *
     */
    public function testAa()
    {
        $doctrine = $this->getDoctrine();
        echo (get_class($doctrine));
        $this->assertTrue(true);
    }
}
