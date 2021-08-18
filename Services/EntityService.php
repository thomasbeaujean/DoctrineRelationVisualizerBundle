<?php

namespace tbn\DoctrineRelationVisualizerBundle\Services;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use tbn\DoctrineRelationVisualizerBundle\Entity\Entity;
use tbn\DoctrineRelationVisualizerBundle\Entity\AssociationEntity;
use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Yaml\Yaml;
use tbn\GetSetForeignNormalizerBundle\Component\Serializer\Normalizer\GetterMethodNormalizerFactory;
use Symfony\Component\Filesystem\Filesystem;
use tbn\DoctrineRelationVisualizerBundle\Entity\Field;

/**
 *
 * @author Thomas BEAUJEAN
 *
 * ref: tbn.entity_relation_visualizer.entity_service
 *
 */
class EntityService
{
    public function __construct($ymlFilePath, private GetterMethodNormalizerFactory $getSetForeignNormalizer, private Registry $doctrine)
    {
        $this->ymlFilePath = $ymlFilePath;
    }

    /**
     * Save the entities position as an array in an yml file
     */
    public function saveEntitiesPositions(array $entities, string $connectionName): void
    {
        $dumper = new Dumper();
        $yaml = $dumper->dump($entities, 10);

        $fs = new Filesystem();

        $filepath = $this->ymlFilePath.'/visualizer-'.$connectionName.'.yml';
        $fs->touch($filepath);

        file_put_contents($filepath, $yaml);
    }

    /**
     * Get an array of entities with their positions
     *
     * @param string $connectionName The connection name
     * @return array:entities
     */
    public function getEntities($connectionName)
    {
        $entities = $this->getEntitiesData($connectionName);

        return $this->getNormalizedEntities($entities, $connectionName);
    }

    /**
     * Get an array of visualizer entities
     *
     * @param $connectionName The connection name
     * @return array:Entity
     */
    protected function getEntitiesData($connectionName)
    {
        $entities = array();

        $em = $this->doctrine->getManager($connectionName);
        $meta = $em->getMetadataFactory()->getAllMetadata();

        foreach ($meta as $m) {
            $doctrineRootEntityName = $m->rootEntityName;

            if ($doctrineRootEntityName !== $m->getName()) {
                $rootEntityName = md5($doctrineRootEntityName);
            } else {
                $rootEntityName = null;
            }

            $entity = new Entity();
            $entityName = $m->getName();
            $entity->setName($entityName);
            $entity->setRootEntityName($rootEntityName);

            $mappings = $m->associationMappings;

            $fieldsMappings = $m->fieldMappings;

            foreach ($fieldsMappings as $fieldsMapping) {
                $field = new Field();
                $field->setName($fieldsMapping['fieldName']);
                $field->setType($fieldsMapping['type']);
                $entity->addField($field);
                unset($field);
            }

            foreach ($mappings as $mapping) {
                //the inherited mapping are not displayed directly
                if (!isset($mapping['inherited'])) {
                    $targetEntityName  = $mapping['targetEntity'];
                    $doctrineAssociationType = $mapping['type'];

                    $isNullable = true;

                    if (isset($mapping['joinColumns'])) {
                        foreach ($mapping['joinColumns'] as $joinColumn) {
                            if (isset($joinColumn['nullable']) && $joinColumn['nullable'] === false) {
                                $isNullable = false;
                            }
                        }
                    }

                    switch ($doctrineAssociationType) {
                        case ClassMetadataInfo::ONE_TO_MANY:
                            $associationType = 'ONE_TO_MANY';
                            break;
                        case ClassMetadataInfo::MANY_TO_MANY:
                            $associationType = 'MANY_TO_MANY';
                            break;
                        case ClassMetadataInfo::MANY_TO_ONE:
                            $associationType = 'MANY_TO_ONE';
                            break;
                        case ClassMetadataInfo::ONE_TO_ONE:
                            $associationType = 'ONE_TO_ONE';
                            break;
                        default:
                            $associationType = $doctrineAssociationType;
                            break;
                    }

                    //if the entity is self referenced, then we create a fake entity suffixed by "-self"
                    if ($targetEntityName === $entityName) {
                        $targetEntityName = $targetEntityName.'-self';
                        $selfEntity = new Entity();
                        $selfEntity->setName($targetEntityName);
                        $selfEntity->getShortName('self');

                        if ($associationType === 'MANY_TO_ONE' || $associationType === 'ONE_TO_ONE') {
                            $targetEntity = new AssociationEntity();
                            $targetEntity->setName($targetEntityName);
                            $targetEntity->setAssociationType($associationType);
                            $targetEntity->setIsNullable($isNullable);
                            $entity->addTargetEntity($targetEntity);

                        } else {
                            $targetEntity = new AssociationEntity();
                            $targetEntity->setName($entityName);
                            $targetEntity->setAssociationType($associationType);
                            $targetEntity->setIsNullable($isNullable);
                            $selfEntity->addTargetEntity($targetEntity);
                        }

                        $entities[] = $selfEntity;
                        unset($selfEntity);
                    } else {
                        $targetEntity = new AssociationEntity();
                        $targetEntity->setName($targetEntityName);
                        $targetEntity->setAssociationType($associationType);
                        $targetEntity->setIsNullable($isNullable);

                        unset($associationType);
                        $entity->addTargetEntity($targetEntity);
                    }

                    unset($associationType);
                    unset($doctrineAssociationType);
                }
            }

            $entities[] = $entity;
        }

        return $entities;
    }

    /**
     *
     * @param array $entities
     * @return array The entities for the view
     */
    protected function getNormalizedEntities($entities, $connectionName)
    {
        //services
        $normalizer = $this->getSetForeignNormalizer;

        $normalizedEntities = array();

        $filepath = $this->ymlFilePath.'/visualizer-'.$connectionName.'.yml';

        $fs = new Filesystem();
        $fs->touch($filepath);

        $content = file_get_contents($filepath);

        $entitiesPositions = Yaml::parse($content);

        //parse entities
        foreach ($entities as $entity) {
            $normalizedEntity = $normalizer->normalize($entity);
            $uuid = $entity->getUuid();

            if (isset($entitiesPositions[$uuid])) {
                if (isset($entitiesPositions[$uuid]['x'])) {
                    $x = $entitiesPositions[$uuid]['x'];
                } else {
                    $x = 1;
                }

                if (isset($entitiesPositions[$uuid]['x'])) {
                    $y = $entitiesPositions[$uuid]['y'];
                } else {
                    $y = 1;
                }
            } else {
                $x = 1;
                $y = 1;
            }

            $normalizedEntities[] = array(
                'x' => $x,
                'y' => $y,
                'entity' => $normalizedEntity,
            );
        }

        return $normalizedEntities;
    }
}
