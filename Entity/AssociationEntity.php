<?php

namespace tbn\DoctrineRelationVisualizerBundle\Entity;

class AssociationEntity
{
    protected $name = null;
    protected $associationType = null;
    protected $isNullable = null;

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setAssociationType(string $associationType)
    {
        $this->associationType = $associationType;
    }

    public function getAssociationType(): string
    {
        return $this->associationType;
    }

    /**
     * The complete namespace
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function getUuid(): string
    {
        return md5($this->getName());
    }

    /**
     * Get the short name, the last entry of the complete namespace
     */
    public function getShortName(): string
    {
        $explodedNames = explode('\\', $this->getName());

        return $explodedNames[count($explodedNames) - 1];
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
