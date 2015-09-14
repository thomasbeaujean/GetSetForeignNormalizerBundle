<?php

namespace tbn\GetSetForeignNormalizerBundle\Tests;

use tbn\GetSetForeignNormalizerBundle\Tests\PHPUnitKernelAware;

/**
 *
 * @author Thomas BEAUJEAN
 *
 */
class TcManyTest extends PHPUnitKernelAware
{
    /**
     *
     */
    public function testManyToOneId()
    {
        $indexTested = 'tcOneReferences';
        $tcReference = new Fixtures\AppTestBundle\Entity\TcOneReference();
        $tcReference->setId(1);
        $tcReference->setName('testname');

        $tcReference2 = new Fixtures\AppTestBundle\Entity\TcOneReference();
        $tcReference2->setId(2);
        $tcReference2->setName('testname2');

        $tcManyReference = new Fixtures\AppTestBundle\Entity\TcManyReference();

        $this->assertNormalizedValue($tcManyReference, $indexTested, []);

        $tcManyReference->addTcOneReference($tcReference);
        $tcManyReference->addTcOneReference($tcReference2);

        $this->assertNormalizedValue($tcManyReference, $indexTested, array(array('id' => 1), array('id' => 2)));

        $normalizer = $this->getService('get_set_foreign_normalizer.normalizer.getter_method_normalizer_factory');
        $normalizedEntity = $normalizer->normalize($tcManyReference, true);
        $this->assertEquals($normalizedEntity[$indexTested][0]['name'], 'testname');
        $this->assertEquals($normalizedEntity[$indexTested][1]['name'], 'testname2');
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
