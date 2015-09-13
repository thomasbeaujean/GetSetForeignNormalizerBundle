<?php

namespace tbn\GetSetForeignNormalizerBundle\Tests;

use tbn\GetSetForeignNormalizerBundle\Tests\PHPUnitKernelAware;

/**
 *
 * @author Thomas BEAUJEAN
 *
 */
class TcOneTest extends PHPUnitKernelAware
{
    /**
     *
     */
    public function testManyToOneId()
    {
        $indexTested = 'tcReference';
        $tcReference = new Fixtures\AppTestBundle\Entity\TcReference();
        $tcReference->setId(1);
        $tcReference->setName('testname');

        $tcOneReference = new Fixtures\AppTestBundle\Entity\TcOneReference();

        $tcOneReference->setTcReference(null);
        $this->assertNormalizedValue($tcOneReference, $indexTested, null);

        $tcOneReference->setTcReference($tcReference);
        $this->assertNormalizedValue($tcOneReference, $indexTested, array('id' => 1));

        $tcOneReference->setTcReference($tcReference);
        $normalizer = $this->getService('get_set_foreign_normalizer.normalizer.getter_method_normalizer_factory');
        $normalizedEntity = $normalizer->normalize($tcOneReference, true);
        $this->assertEquals($normalizedEntity[$indexTested]['name'], 'testname');
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


    /**
     *
     * @param objcet  $entity
     * @param string  $field
     * @param unknown $expectedValue
     */
    protected function assertDeepNormalizedValue($entity, $field, $expectedValue)
    {
        $normalizer = $this->getService('get_set_foreign_normalizer.normalizer.getter_method_normalizer_factory');
        $normalizedEntity = $normalizer->normalize($entity, true);

        zdebug($normalizedEntity);
        $this->assertEquals($normalizedEntity[$field], $expectedValue);
    }
}
