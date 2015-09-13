<?php

namespace tbn\GetSetForeignNormalizerBundle\Tests;

include '/var/www/zdebug/zdebug.php';


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

    /**
     *
     */
    public function testReferenceDate()
    {
        $expectedFormat = 'Y-m-d';
        $indexTested = 'testDate';

        $normalizer = $this->getService('get_set_foreign_normalizer');
        $entity = new Fixtures\AppTestBundle\Entity\TcReference();

        $entity->setTestDate(null);
        $normalizedEntity = $normalizer->normalize($entity);
        $this->assertNull($normalizedEntity[$indexTested]);

        $newValue = new \DateTime();
        $entity->setTestDate($newValue);
        $normalizedEntity = $normalizer->normalize($entity);
        $this->assertEquals($normalizedEntity[$indexTested], $newValue->format($expectedFormat));
    }

    /**
     *
     */
    public function testTime()
    {
        $expectedFormat = 'H:i:s';
        $indexTested = 'testTime';

        $normalizer = $this->getService('get_set_foreign_normalizer');
        $entity = new Fixtures\AppTestBundle\Entity\TcReference();

        $entity->setTestTime(null);
        $normalizedEntity = $normalizer->normalize($entity);
        $this->assertNull($normalizedEntity[$indexTested]);

        $newValue = new \DateTime();
        $entity->setTestTime($newValue);
        $normalizedEntity = $normalizer->normalize($entity);
        $this->assertEquals($normalizedEntity[$indexTested], $newValue->format($expectedFormat));
    }

    /**
     *
     */
    public function testReferenceDatetime()
    {
        $expectedFormat = 'Y-m-d H:i:s';
        $indexTested = 'testDate';

        $normalizer = $this->getService('get_set_foreign_normalizer');
        $entity = new Fixtures\AppTestBundle\Entity\TcReference();

        $entity->setTestDatetime(null);
        $normalizedEntity = $normalizer->normalize($entity);
        $this->assertNull($normalizedEntity[$indexTested]);

        $newValue = new \DateTime();
        $entity->setTestDatetime($newValue);
        $normalizedEntity = $normalizer->normalize($entity);
        $this->assertEquals($normalizedEntity[$indexTested], $newValue->format($expectedFormat));
    }
}
