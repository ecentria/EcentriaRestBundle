<?php
/*
 * This file is part of the Ecentria software.
 *
 * (c) 2014, OpticsPlanet, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\CoreRestBundle\Model\CRUD;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * CRUD unit of work
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class CRUDUnitOfWork
{
    /**
     * CRUD entities to insert
     *
     * @var ArrayCollection|CRUDEntityInterface[]
     */
    private $insertions;

    /**
     * CRUD entities to update
     *
     * @var ArrayCollection|CRUDEntityInterface[]
     */
    private $updates;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->insertions = new ArrayCollection();
        $this->updates = new ArrayCollection();
    }

    /**
     * Add insertion
     *
     * @param CRUDEntityInterface $entity
     * @return self
     */
    public function insert(CRUDEntityInterface $entity)
    {
        $this->insertions->add($entity);
        return $this;
    }

    /**
     * Add insertion
     *
     * @param CRUDEntityInterface $entity
     * @return self
     */
    public function update(CRUDEntityInterface $entity)
    {
        $this->updates->add($entity);
        return $this;
    }

    /**
     * Insertions getter
     *
     * @return ArrayCollection|CRUDEntityInterface[]
     */
    public function getInsertions()
    {
        return $this->insertions;
    }

    /**
     * Updates getter
     *
     * @return ArrayCollection|CRUDEntityInterface[]
     */
    public function getUpdates()
    {
        return $this->updates;
    }

    /**
     * All
     *
     * @return ArrayCollection
     */
    public function all()
    {
        return new ArrayCollection(array_merge(
            $this->insertions->toArray(),
            $this->updates->toArray()
        ));
    }
}
