<?php

namespace tbn\DoctrineRelationVisualizerBundle\Tests\src\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
class MyClass
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(type: Types::INTEGER)]
    private int $number;

    #[ORM\Column(type: Types::STRING)]
    private string $name;

    /**
     * @var Collection<ForeignClass>
     */
    #[ORM\OneToMany(targetEntity: ForeignClass::class, mappedBy: 'myClass')]
    private Collection $foreignClasses;

    /**
     * @var array<int>
     */
    private array $references;
}
