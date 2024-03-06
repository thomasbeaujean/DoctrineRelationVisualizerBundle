<?php

namespace Tbn\DoctrineRelationVisualizerBundle\Entity;

class Entity
{
    protected $name = null;
    protected $rootEntityName = null;
    protected $targetEntities = [];
    protected $isTargetEntities = [];
    protected $fields = [];

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setRootEntityName(?string $rootEntityName): void
    {
        $this->rootEntityName = $rootEntityName;
    }

    public function getRootEntityName(): ?string
    {
        return $this->rootEntityName;
    }

    public function getType(): string
    {
        if (count($this->getTargetEntities()) === 0) {
            $type = 'WeakEntity';
        } else {
            $type = 'Entity';
        }

        return $type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUuid(): string
    {
        return md5($this->getName());
    }

    public function getShortName(): string
    {
        $explodedNames = explode('\\', $this->getName());

        return $explodedNames[count($explodedNames) - 1];
    }

    public function addTargetEntity($targetEntity): void
    {
        $this->targetEntities[] = $targetEntity;
    }

    public function addIsTargetEntity(bool $isTargetEntity): void
    {
        $this->isTargetEntities[] = $isTargetEntity;
    }

    public function getIsTargetEntities(): array
    {
        return $this->isTargetEntities;
    }

    public function getTargetEntities(): array
    {
        return $this->targetEntities;
    }

    public function addField(Field $field): void
    {
        $this->fields[] = $field;
    }

    public function getFields(): array
    {
        return $this->fields;
    }
}
