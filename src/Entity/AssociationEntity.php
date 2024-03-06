<?php

namespace Tbn\DoctrineRelationVisualizerBundle\Entity;

class AssociationEntity
{
    protected $name = null;
    protected $associationType = null;
    protected $isNullable = null;

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setAssociationType(string $associationType): void
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

    public function setIsNullable($isNullable): void
    {
        $this->isNullable = $isNullable;
    }

    public function getIsNullable()
    {
        return $this->isNullable;
    }
}
