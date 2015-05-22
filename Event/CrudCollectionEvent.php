<?php
/*
 * This file is part of the ecentria group, inc. software.
 *
 * (c) 2015, ecentria group, inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\EcentriaRestBundle\Event;

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
