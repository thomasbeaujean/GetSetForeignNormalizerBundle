<?php

namespace tbn\GetSetForeignNormalizerBundle\Tests;

use tbn\GetSetForeignNormalizerBundle\Tests\PHPUnitKernelAware;

/**
 *
 * @author Thomas BEAUJEAN
 *
 */
class FieldTest extends PHPUnitKernelAware
{
    /**
     *
     */
    public function testReferenceBoolean()
    {
        $entity = new Fixtures\AppTestBundle\Entity\TcReference();
        $indexTested = 'testBoolean';

        $entity->setTestBoolean(null);
        $this->assertNormalizedValue($entity, $indexTested, null);

        $entity->setTestBoolean(true);
        $this->assertNormalizedValue($entity, $indexTested, true);

        $entity->setTestBoolean(false);
        $this->assertNormalizedValue($entity, $indexTested, false);
    }

    /**
     *
     */
    public function testReferenceInteger()
    {
        $entity = new Fixtures\AppTestBundle\Entity\TcReference();
        $indexTested = 'testInteger';

        $entity->setTestInteger(null);
        $this->assertNormalizedValue($entity, $indexTested, null);

        $newValue = 123;
        $entity->setTestInteger($newValue);
        $this->assertNormalizedValue($entity, $indexTested, $newValue);
    }

    /**
     *
     */
    public function testReferenceBigint()
    {
        $entity = new Fixtures\AppTestBundle\Entity\TcReference();
        $indexTested = 'testBigint';

        $entity->setTestBigint(null);
        $this->assertNormalizedValue($entity, $indexTested, null);

        $newValue = 123;
        $entity->setTestBigint($newValue);
        $this->assertNormalizedValue($entity, $indexTested, $newValue);
    }

    /**
     *
     */
    public function testReferenceDecimal()
    {
        $entity = new Fixtures\AppTestBundle\Entity\TcReference();
        $indexTested = 'testDecimal';

        $entity->setTestDecimal(null);
        $this->assertNormalizedValue($entity, $indexTested, null);

        $newValue = 123.45;
        $entity->setTestDecimal($newValue);
        $this->assertNormalizedValue($entity, $indexTested, $newValue);
    }

    /**
     *
     */
    public function testReferenceString()
    {
        $entity = new Fixtures\AppTestBundle\Entity\TcReference();
        $indexTested = 'testString';

        $entity->setTestString(null);
        $this->assertNormalizedValue($entity, $indexTested, null);

        $newValue = 'some string';
        $entity->setTestString($newValue);
        $this->assertNormalizedValue($entity, $indexTested, $newValue);
    }

    /**
     *
     */
    public function testReferenceText()
    {
        $entity = new Fixtures\AppTestBundle\Entity\TcReference();
        $indexTested = 'testText';

        $entity->setTestText(null);
        $this->assertNormalizedValue($entity, $indexTested, null);

        $newValue = 'some string';
        $entity->setTestText($newValue);
        $this->assertNormalizedValue($entity, $indexTested, $newValue);
    }

    /**
     *
     */
    public function testTime()
    {
        $expectedFormat = 'H:i:s';
        $indexTested = 'testTime';

        $entity = new Fixtures\AppTestBundle\Entity\TcReference();

        $entity->setTestTime(null);
        $this->assertNormalizedValue($entity, $indexTested, null);

        $newValue = new \DateTime();
        $entity->setTestTime($newValue);
        $this->assertNormalizedValue($entity, $indexTested, $newValue->format($expectedFormat));
    }

    /**
     *
     */
    public function testReferenceDatetime()
    {
        $expectedFormat = 'Y-m-d H:i:s';
        $indexTested = 'testDatetime';

        $entity = new Fixtures\AppTestBundle\Entity\TcReference();

        $entity->setTestDatetime(null);
        $this->assertNormalizedValue($entity, $indexTested, null);

        $newValue = new \DateTime();
        $entity->setTestDatetime($newValue);
        $this->assertNormalizedValue($entity, $indexTested, $newValue->format($expectedFormat));
    }

    /**
     *
     * @param objcet  $entity
     * @param string  $field
     * @param unknown $expectedValue
     */
    protected function assertNormalizedValue($entity, $field, $expectedValue)
    {
        $normalizer = $this->getService('get_set_foreign_normalizer.normalizer.getter_method_normalizer_factory');
        $normalizedEntity = $normalizer->normalize($entity);
        $this->assertEquals($normalizedEntity[$field], $expectedValue);
    }
}
