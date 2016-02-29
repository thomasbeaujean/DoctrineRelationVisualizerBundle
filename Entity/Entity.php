<?php

namespace tbn\DoctrineRelationVisualizerBundle\Entity;

/**
 *
 * @author Thomas BEAUJEAN
 *
 */
class Entity
{
    protected $name = null;
    protected $rootEntityName = null;
    protected $targetEntities = [];
    protected $isTargetEntities = [];
    protected $fields = [];

    /**
     *
     * @param String $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     *
     * @param String $rootEntityName
     */
    public function setRootEntityName($rootEntityName)
    {
        $this->rootEntityName = $rootEntityName;
    }

    /**
     *
     * @return String $rootEntityName
     */
    public function getRootEntityName()
    {
        return $this->rootEntityName;
    }

    /**
     *
     * @return string
     */
    public function getType()
    {
        if (count($this->getTargetEntities()) === 0) {
            $type = 'WeakEntity';
        } else {
            $type = 'Entity';
        }

        return $type;
    }

    /**
     * The complete namespace
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the uuid based on the name
     *
     * @return string
     */
    public function getUuid()
    {
        return md5($this->getName());
    }

    /**
     * Get the short name, the last entry of the complete namespace
     *
     * @return array
     */
    public function getShortName()
    {
        $explodedNames = explode('\\', $this->getName());

        return $explodedNames[count($explodedNames) - 1];
    }

    /**
     *
     * @param unknown $targetEntity
     */
    public function addTargetEntity($targetEntity)
    {
        $this->targetEntities[] = $targetEntity;
    }

    /**
     *
     * @param Boolean $isTargetEntity
     */
    public function addIsTargetEntity($isTargetEntity)
    {
        $this->isTargetEntities[] = $isTargetEntity;
    }

    /**
     * @return Boolean
     */
    public function getIsTargetEntities()
    {
        return $this->isTargetEntities;
    }

    /**
     *
     * @return Ambigous <multitype:, unknown>
     */
    public function getTargetEntities()
    {
        return $this->targetEntities;
    }

    /**
     *
     * @param Field $field
     */
    public function addField(Field $field)
    {
        $this->fields[] = $field;
    }

    /**
     * @return array:Field
     */
    public function getFields()
    {
        return $this->fields;
    }
}
