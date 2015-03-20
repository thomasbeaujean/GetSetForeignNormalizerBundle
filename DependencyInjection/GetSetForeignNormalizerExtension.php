<?php

namespace tbn\GetSetForeignNormalizerBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;



/**
 * GetSetForeignNormalizeExtension.
 *
 * @author Thomas Beaujean
 */
class GetSetForeignNormalizerExtension extends Extension
{
    /**
     * Load the eventListener
     *
     * @param array            $configs   The config
     * @param ContainerBuilder $container The container
     *
     * @return nothing
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
