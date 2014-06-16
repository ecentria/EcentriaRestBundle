<?php

namespace Ecentria\Libraries\CoreRestBundle\HAL;

class ObjectHandlerChain
{
    private $objects;

    public function __construct()
    {
        $this->objects = [];
    }

    public function addObject($object, $alias)
    {
        $this->objects[$alias] = $object;
    }

    public function getObject($alias)
    {
        if (array_key_exists($alias, $this->objects)) {
            return $this->objects[$alias];
        }
    }
}