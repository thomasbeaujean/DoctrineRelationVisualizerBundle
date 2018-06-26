<?php

namespace tbn\DoctrineRelationVisualizerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\HttpKernel\Kernel;

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

        // code for Symfony 2.x & 3.x
        $positionFilepathDefaultValue = '%kernel.root_dir%/config';
        if (Kernel::VERSION_ID >= 40000) {
            // code for Symfony 4.x
            $positionFilepathDefaultValue = '%kernel.project_dir%/src/DoctrineVisualizerBundle';
        }

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
