<?php

namespace tbn\DoctrineRelationVisualizerBundle\Entity;

/**
 *
 * @author Thomas BEAUJEAN
 *
 */
class Field
{
    protected $name = null;
    protected $type = null;

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
    public function setType($associationType)
    {
        $this->type = $associationType;
    }

    /**
     *
     * @return String The type
     */
    public function getType()
    {
        return $this->type;
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
}
