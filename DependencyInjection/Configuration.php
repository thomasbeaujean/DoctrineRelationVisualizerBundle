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
     * (non-PHPdoc)
     * @see \Symfony\Component\Config\Definition\ConfigurationInterface::getConfigTreeBuilder()
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('tbn_doctrine_relation_visualizer');

        $rootNode->children()
            ->scalarNode('position_filepath')->defaultValue('%kernel.root_dir%/config/visualizer.yml')
        ->end();

        return $treeBuilder;
    }
}
