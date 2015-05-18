<?php
/*
 * This file is part of the ecentria group, inc. software.
 *
 * (c) 2015, ecentria group, inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Ecentria\Libraries\CoreRestBundle\Event;

use Ecentria\Libraries\CoreRestBundle\Model\CRUD\CrudEntityInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Create collection event
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class CrudEvent extends Event
{
    /**
     * Entity
     *
     * @var object
     */
    private $entity;

    /**
     * Created at
     *
     * @var \DateTime
     */
    private $createdAt;

    /**
     * Entity getter
     *
     * @return object
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Constructor
     *
     * @param CrudEntityInterface $entity entity
     */
    public function __construct(CrudEntityInterface $entity)
    {
        $this->entity = $entity;
        $this->createdAt = new \DateTime();
    }
}
