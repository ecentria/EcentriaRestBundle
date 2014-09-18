<?php
/*
 * This file is part of the Ecentria software.
 *
 * (c) 2014, OpticsPlanet, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\CoreRestBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;

/**
 * Collection Response
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 *
 * @Hateoas\Relation(
 *      "service-transaction",
 *      exclusion = @Hateoas\Exclusion(
 *          excludeIf = "expr(object.getTransaction() === null)"
 *      ),
 *      href = @Hateoas\Route(
 *          "get_transaction",
 *          parameters = {
 *              "id" = "expr(object.getTransaction().getId())"
 *          },
 *          absolute = true
 *      ),
 *      embedded = "expr(object.getTransaction())"
 * )
 */
class CollectionResponse
{
    /**
     * Array collection of entities
     *
     * @var ArrayCollection
     */
    private $items;

    /**
     * @Serializer\Exclude
     */
    private $transaction;

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
     * @param \Doctrine\Common\Collections\ArrayCollection $items
     * @return self
     */
    public function setItems($items)
    {
        $this->items = $items;
        return $this;
    }

    /**
     * Items getter
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Transaction setter
     *
     * @param mixed $transaction
     * @return self
     */
    public function setTransaction($transaction)
    {
        $this->transaction = $transaction;
        return $this;
    }

    /**
     * Transaction getter
     *
     * @return mixed
     */
    public function getTransaction()
    {
        return $this->transaction;
    }
}

