<?php
/*
 * This file is part of the ecentria group, inc. software.
 *
 * (c) 2015, ecentria group, inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\EcentriaRestBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

use Ecentria\Libraries\EcentriaRestBundle\Model\Transactional\TransactionalInterface,
    Ecentria\Libraries\EcentriaRestBundle\Model\Transactional\TransactionalTrait;

use JMS\Serializer\Annotation as Serializer;

/**
 * Collection Response
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class CollectionResponse implements TransactionalInterface
{
    use TransactionalTrait;

    /**
     * Array collection of entities
     *
     * @var ArrayCollection
     */
    private $items;

    /**
     * Constructor
     *
     * @param ArrayCollection $collection
     */
    public function __construct(ArrayCollection $collection)
    {
        $this->items = $collection;
    }

    /**
     * Items setter
     *
     * @param ArrayCollection $items
     * @return CollectionResponse
     */
    public function setItems(ArrayCollection $items)
    {
        $this->items = $items;
        return $this;
    }

    /**
     * Items getter
     *
     * @return ArrayCollection
     */
    public function getItems()
    {
        return $this->items;
    }
}
