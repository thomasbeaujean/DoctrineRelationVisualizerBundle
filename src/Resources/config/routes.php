<?php

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes) {
    $routes
        ->import('@DoctrineRelationVisualizerBundle/Controller/', 'attribute')
        ->prefix('/');
};
