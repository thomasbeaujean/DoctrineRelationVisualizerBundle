<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Tbn\DoctrineRelationVisualizerBundle\Controller\VisualizerController;
use Tbn\DoctrineRelationVisualizerBundle\DependencyInjection\DoctrineRelationVisualizerExtension;
use Tbn\DoctrineRelationVisualizerBundle\Services\EntityService;
use Tbn\DoctrineRelationVisualizerBundle\Services\Persister;

return function (ContainerConfigurator $configurator) {
    $services = $configurator->services();

    $services->set(VisualizerController::class)
        ->public()
        ->args([
            service(EntityService::class),
            service(Persister::class),
            service('serializer'),
            param('doctrine.default_entity_manager'),
            param('doctrine.entity_managers'),
            param('tbn.entity_relation_visualizer.display_columns'),
            param('tbn.entity_relation_visualizer.area_width'),
            param('tbn.entity_relation_visualizer.area_height'),
        ])
        ->call('setContainer', [service('Psr\Container\ContainerInterface')])
        ->tag('container.service_subscriber')
    ;

    $services->set(EntityService::class)
        ->public()
        ->args([
            service('doctrine')
        ])
    ;

    $services->set(Persister::class)
        ->public()
        ->args([
            param('tbn.entity_relation_visualizer.position_filepath')
        ])
    ;

    $services->set(DoctrineRelationVisualizerExtension::class);
};
