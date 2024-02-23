<?php

namespace tbn\DoctrineRelationVisualizerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('doctrine_relation_visualizer');
        $rootNode = $treeBuilder->getRootNode();

        $positionFilepathDefaultValue = '%kernel.project_dir%/config';

        $rootNode
        ->children()
            ->scalarNode('position_filepath')->defaultValue($positionFilepathDefaultValue)->end()
            ->booleanNode('display_columns')->defaultTrue()->end()
            ->integerNode('area_width')->defaultValue(4000)->end()
            ->integerNode('area_height')->defaultValue(3000)->end()
        ->end();

        return $treeBuilder;
    }
}
