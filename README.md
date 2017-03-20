# DoctrineRelationVisualizerBundle

This bundle allows to visualize the relation between doctrine entities.

The diagram is always in sync with the application entities.

# Installation

		composer require --dev "tbn/doctrinerelationvisualizer-bundle"

Publish assets

		php app/console  assets:install

## Enable the bundle in the AppKernel for the dev environment
		
		if (in_array($this->getEnvironment(), array('dev', 'test'))) {
			 ...
            $bundles[] = new tbn\DoctrineRelationVisualizerBundle\DoctrineRelationVisualizerBundle();
	    
            //The DoctrineRelationVisualizerBundle requires the bundle GetSetForeignNormalizerBundle
            $bundles[] = new \tbn\GetSetForeignNormalizerBundle\GetSetForeignNormalizerBundle();

            ...

## Add routing 
In routing_dev.yml

		tbn_doctrine_relation_visualizer:
			resource: "@DoctrineRelationVisualizerBundle/Resources/config/routing.yml"

# Usage

Go to the url:

		http://your_app/_visualizer
	
Sort your entities to have a correct diagram

