<?php

namespace tbn\DoctrineRelationVisualizerBundle\Entity;

class Field
{
    protected $name = null;
    protected $type = null;

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setType(string $associationType): void
    {
        $this->type = $associationType;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
