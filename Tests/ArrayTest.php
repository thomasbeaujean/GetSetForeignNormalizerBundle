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
     * Clean database before feature starts
     *
     * @BeforeFeature
     */
    public function setUp()
    {
        parent::setUp();
    }

    /**
     *
     */
    public function testReferenceBoolean()
    {
        $normalizer = $this->getService('get_set_foreign_normalizer');
        $entity = new Fixtures\AppTestBundle\Entity\TcReference();

        $entity->setTestBoolean(null);
        $normalizedEntity = $normalizer->normalize($entity);
        $this->assertNull($normalizedEntity['testBoolean']);

        $entity->setTestBoolean(true);
        $normalizedEntity = $normalizer->normalize($entity);
        $this->assertTrue($normalizedEntity['testBoolean']);

        $entity->setTestBoolean(false);
        $normalizedEntity = $normalizer->normalize($entity);
        $this->assertFalse($normalizedEntity['testBoolean']);
    }

    /**
     *
     */
    public function testReferenceInteger()
    {
        $normalizer = $this->getService('get_set_foreign_normalizer');
        $entity = new Fixtures\AppTestBundle\Entity\TcReference();

        $entity->setTestInteger(null);
        $normalizedEntity = $normalizer->normalize($entity);
        $this->assertNull($normalizedEntity['testInteger']);

        $newValue = 123;
        $entity->setTestInteger($newValue);
        $normalizedEntity = $normalizer->normalize($entity);
        $this->assertEquals($normalizedEntity['testInteger'], $newValue);
    }
}
