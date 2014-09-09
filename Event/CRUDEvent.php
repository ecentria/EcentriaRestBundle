<?php
/*
 * This file is part of the OpCart software.
 *
 * (c) 2014, OpticsPlanet, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Ecentria\Libraries\CoreRestBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Create collection event
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class CRUDEvent extends Event
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
     */
    public function __construct($entity)
    {
        $this->entity = $entity;
        $this->createdAt = new \DateTime();
    }
}
