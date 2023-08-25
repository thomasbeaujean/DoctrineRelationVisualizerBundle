<?php

namespace tbn\DoctrineRelationVisualizerBundle\Tests\src\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class ForeignClass
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\ManyToOne(targetEntity: MyClass::class)]
    private ?MyClass $myClass;

    // public function setId(int $value): void
    // {
    //     $this->id = $value;
    // }

    // public function getId(): int
    // {
    //     return $this->id;
    // }

    // public function setMyClass(?MyClass $value): void
    // {
    //     $this->myClass = $value;
    // }

    // public function getMyClass(): ?MyClass
    // {
    //     return $this->myClass;
    // }
}
