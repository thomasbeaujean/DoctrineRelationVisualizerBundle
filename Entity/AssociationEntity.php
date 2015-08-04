<?php

namespace tbn\DoctrineRelationVisualizerBundle\Entity;

/**
 *
 * @author Thomas BEAUJEAN
 *
 */
class AssociationEntity
{
    protected $name = null;
    protected $associationType = null;
    protected $isNullable = null;

    /**
     *
     * @param unknown $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * The type of association
     * @param string $associationType
     */
    public function setAssociationType($associationType)
    {
        $this->associationType = $associationType;
    }

    /**
     *
     * @return String The type of association
     */
    public function getAssociationType()
    {
        return $this->associationType;
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
     * Get thte uuid based on the name
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
        $shortName = $explodedNames[count($explodedNames) - 1];

        return $shortName;
    }

    /**
     * Set the $isNullable
     * @param unknown $isNullable
     */
    public function setIsNullable($isNullable)
    {
        $this->isNullable = $isNullable;
    }

    /**
     * Get the $isNullable
     * @return unknown $isNullable
     */
    public function getIsNullable()
    {
        return $this->isNullable;
    }
}
