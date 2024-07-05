# DoctrineRelationVisualizerBundle

This bundle allows to visualize the relation between doctrine entities.

The diagram is always in sync with the application entities.

# Installation

    composer require --dev "tbn/doctrinerelationvisualizer-bundle"

Publish assets

    php app/console assets:install

## Enable the bundle in the AppKernel for the dev environment

Add the bundle to `config/bundles.php`

    return [
        ...
        tbn\DoctrineRelationVisualizerBundle\DoctrineRelationVisualizerBundle::class => ['dev' => true],
        ...
    ];

## Add routing 
Add the file `config/routes/dev/visualizer.yaml`

    tbn_doctrine_relation_visualizer:
        resource: "@DoctrineRelationVisualizerBundle/Resources/config/routing.yml"

## Customize bundle (optionnal)

Add the file `config/packages/dev/doctrine_relation_visualizer.yaml`

    doctrine_relation_visualizer:
        position_filepath: '%kernel.project_dir%/config'
        display_columns: true
        area_width: 4000
        area_height: 3000

# Usage

Go to the url:

    http://your_app/_visualizer
	
Sort your entities to have a correct diagram

