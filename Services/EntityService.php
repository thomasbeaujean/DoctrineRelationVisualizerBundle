<?php

namespace tbn\DoctrineRelationVisualizerBundle\Services;

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use tbn\DoctrineRelationVisualizerBundle\Entity\Entity;
use tbn\DoctrineRelationVisualizerBundle\Entity\AssociationEntity;
use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Yaml\Yaml;
use tbn\GetSetForeignNormalizerBundle\Component\Serializer\Normalizer\GetSetPrimaryMethodNormalizer;
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
    /**
     *
     * @param String $ymlFilePath
     * @param GetSetPrimaryMethodNormalizer $getSetForeignNormalizer
     * @param Doctrine $doctrine
     */
    public function __construct($ymlFilePath, GetSetPrimaryMethodNormalizer $getSetForeignNormalizer, $doctrine)
    {
        $this->ymlFilePath = $ymlFilePath;
        $this->getSetForeignNormalizer = $getSetForeignNormalizer;
        $this->doctrine = $doctrine;

        $fs = new Filesystem();

        if (!$fs->exists($this->ymlFilePath)) {
            $fs->touch($this->ymlFilePath);
        }
    }

    /**
     * Save the entities position as an array in an yml file
     *
     * @param array $entities
     */
    public function saveEntitiesPositions($entities)
    {
        $dumper = new Dumper();
        $yaml = $dumper->dump($entities, 10);

        file_put_contents($this->ymlFilePath, $yaml);
    }

    /**
     * Get an array of entities with their positions
     *
     * @return array:entities
     */
    public function getEntities()
    {
        $entities = $this->getEntitiesData();

        $normalizedEntities = $this->getNormalizedEntities($entities);

        return $normalizedEntities;
    }

    /**
     * Get an array of visualizer entities
     *
     * @return array:Entity
     */
    protected function getEntitiesData()
    {
        $entities = array();

        $em = $this->doctrine->getManager();
        $meta = $em->getMetadataFactory()->getAllMetadata();

        foreach ($meta as $m) {

            $doctrineRootEntityName = $m->rootEntityName;

            zdebug($m);

            if ($doctrineRootEntityName !== $m->getName()) {
                $rootEntityName = md5($doctrineRootEntityName);
            } else {
                $rootEntityName = null;
            }

            $entity = new Entity();
            $entity->setName($m->getName());
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
                $isOwningSide = $mapping['isOwningSide'];
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

                $targetEntity = new AssociationEntity();
                $targetEntity->setName($targetEntityName);
                $targetEntity->setAssociationType($associationType);
                $targetEntity->setIsNullable($isNullable);

                unset($associationType);
                unset($doctrineAssociationType);

                $entity->addTargetEntity($targetEntity);
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
    protected function getNormalizedEntities($entities)
    {
        //services
        $normalizer = $this->getSetForeignNormalizer;

        $normalizedEntities = array();

        $content = file_get_contents($this->ymlFilePath);

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
                'entity' => $normalizedEntity
            );
        }

        return $normalizedEntities;
    }
}
