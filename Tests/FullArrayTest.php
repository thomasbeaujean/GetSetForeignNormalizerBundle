<?php

namespace tbn\GetSetForeignNormalizerBundle\Tests;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Component\Yaml\Yaml;
use tbn\GetSetForeignNormalizerBundle\Tests\PHPUnitKernelAware;

/**
 *
 * @author Thomas BEAUJEAN
 *
 */
class FullArrayTest extends PHPUnitKernelAware
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
     * Purge database
     *
     */
    protected function resetDatabase()
    {
        $loader = new Loader();
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->getEm(), $purger);
        $executor->execute($loader->getFixtures());
    }

    /**
     *
     * @param string $filepath
     */
    protected function loadInputFixtures($filepath)
    {
        //load basic fixtures
        \Nelmio\Alice\Fixtures::load($filepath, $this->getEm());
    }

    /**
     *
     */
    public function zztestNormalizeTcReference()
    {
        $normalizer = $this->getService('get_set_foreign_normalizer');

        $this->resetDatabase();
        $this->loadInputFixtures(__DIR__.'/Normalization/fixtures/input/reference_1.yml');

        $entities = $this->findAll('TcReference');
        $normalizedEntities = $normalizer->normalize($entities);
        $expectedNormalisationFile = __DIR__.'/Normalization/fixtures/ouput/reference_1.yml';

        $this->assertExpectedNormalisation('reference_1', $normalizedEntities, $expectedNormalisationFile);
    }

    /**
     *
     * @param type $expectedNormalisationFile
     */
    protected function assertExpectedNormalisation($testname, $normalizedEntities, $expectedNormalisationFile)
    {
        $yaml = Yaml::parse($expectedNormalisationFile);

        //echo  print_r($normalizedEntities, true);
        //echo  print_r($yaml, true);
        $this->assertEquals($normalizedEntities, $yaml, $testname.' is not correctly parsed into '.basename($expectedNormalisationFile));
        //$this->assertEquals($normalizedEntities, $yaml, $testname.' is not correctly parsed into '.basename($expectedNormalisationFile). ' DIFF ='.$this->getArrayDiff($normalizedEntities, $yaml));
    }


    /**
     * Gind all entries of a entity
     *
     * @param type $entityName
     * @return type
     */
    protected function findAll($entityName)
    {
        $em = $this->getEm();
        $repository = $em->getRepository('AppTestBundle:'.$entityName);
        $entities = $repository->findAll();

        return $entities;
    }

    /**
     *
     * @param type $a
     * @param type $b
     * @return int
     */
    public static function keyCompareFunc($a, $b)
    {
        if ($a === $b) {
            return 0;
        }

        return ($a > $b) ? 1 : -1;
    }

    /**
     *
     * @param type $array1
     * @param type $array2
     * @return type
     */
    protected function getArrayDiff($array1, $array2)
    {
        $result = array_diff_uassoc($array1, $array2, 'tbn\GetSetForeignNormalizerBundle\Tests\ArrayTest::keyCompareFunc');

        $result = print_r($result, true);

        return $result;
    }
}
