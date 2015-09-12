<?php

namespace tbn\GetSetForeignNormalizerBundle\Tests;

/**
 *
 * @author Thomas BEAUJEAN
 *
 */
class PHPUnitKernelAware extends \PHPUnit_Framework_TestCase
{
    /**
     *
     */
    public function setUp()
    {
        parent::setUp();

        static::$kernel = static::createKernel();
        static::$kernel->boot();
    }

    /**
     * Get the default entity manager
     *
     * @return EntityManager
     */
    protected function getEm()
    {
        return $this->getService('doctrine.orm.entity_manager');
    }

    /**
     * Get the doctrine service
     *
     * @return Dcotrine
     */
    protected function getDoctrine()
    {
        return $this->getService('doctrine');
    }

    /**
     * Get the validator service
     *
     * @return Validator
     */
    protected function getValidator()
    {
        return $this->getService('validator');
    }

    /**
     * Get the router service
     *
     * @return Router
     */
    protected function getRouter()
    {
        return $this->getService('router');
    }

    /**
     *
     * @param type $route
     * @param type $parameters
     * @return type
     */
    protected function generateUrl($route, $parameters = [])
    {
        return $this->getRouter()->generate($route, $parameters);
    }

    /**
     * Get a service
     *
     * @param string $name
     *
     * @return type
     */
    protected function getService($name)
    {
        return $this->getBootedKernel()->getContainer()->get($name);
    }

    /**
     * Has the kernel a service
     *
     * @param string $name
     *
     * @return type
     */
    protected function hasService($name)
    {
        return $this->getBootedKernel()->getContainer()->has($name);
    }

    /**
     *
     * @return type
     */
    protected function getBootedKernel()
    {
        $this->kernel = $this->createKernel();

        if (!$this->kernel->isBooted()) {
            $this->kernel->boot();
        }

        return $this->kernel;
    }
}
