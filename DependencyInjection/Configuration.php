<?php

namespace tbn\GetSetForeignNormalizerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\NodeInterface;

/**
 * GetSetForeignNormalizerBundle configuration structure.
 *
 * @author Thomas Beaujean
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree.
     *
     * @return NodeInterface
     */
    public function getConfigTreeBuilder()
    {
        return new TreeBuilder('get_set_foreign_normalizer');
    }
}
