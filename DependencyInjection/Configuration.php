<?php

namespace tbn\DoctrineRelationVisualizerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 *
 * @author Thomas BEAUJEAN
 *
 */
class Configuration implements ConfigurationInterface
{
    /**
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('doctrine_relation_visualizer');

        $rootNode
        ->children()
            ->scalarNode('position_filepath')->defaultValue('%kernel.root_dir%/config')->end()
            ->booleanNode('display_columns')->defaultTrue()->end()
        ->end();

        return $treeBuilder;
    }
}
