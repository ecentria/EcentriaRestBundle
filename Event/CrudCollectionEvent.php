<?php
/*
 * This file is part of the Ecentria software.
 *
 * (c) 2014, OpticsPlanet, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Ecentria\Libraries\CoreRestBundle\Event;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\EventDispatcher\Event;

/**
 * Create collection event
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class CrudCollectionEvent extends Event
{
    /**
     * ArrayCollection
     *
     * @var ArrayCollection
     */
    private $collection;

    /**
     * Getter
     *
     * @return ArrayCollection
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * Constructor
     *
     * @param ArrayCollection $collection collection
     */
    public function __construct(ArrayCollection $collection)
    {
        $this->collection = $collection;
    }
}
