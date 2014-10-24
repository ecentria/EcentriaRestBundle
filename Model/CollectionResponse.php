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
use Ecentria\Libraries\CoreRestBundle\Entity\Transaction;
use Ecentria\Libraries\CoreRestBundle\Interfaces\EmbeddedInterface;
use Ecentria\Libraries\CoreRestBundle\Traits\EmbeddedTrait;
use JMS\Serializer\Annotation as Serializer;

/**
 * Collection Response
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class CollectionResponse implements EmbeddedInterface
{
    use EmbeddedTrait;

    /**
     * Array collection of entities
     *
     * @var ArrayCollection
     */
    private $items;

    /**
     * Transaction
     * @var Transaction|null
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
     * @param ArrayCollection $items
     * @return self
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

    /**
     * Transaction setter
     *
     * @param Transaction|null $transaction
     * @return self
     */
    public function setTransaction(Transaction $transaction = null)
    {
        $this->transaction = $transaction;
        return $this;
    }

    /**
     * Transaction getter
     *
     * @return Transaction|null
     */
    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * Setting association to show
     *
     * @param mixed $value
     */
    public function setInheritedShowAssociations($value)
    {
        foreach ($this->getItems() as $item)
        {
            if ($item instanceof EmbeddedInterface) {
                try {
                    $item->setShowAssociations((bool) $value);
                } catch (\Exception $e) {
                    // Reference.
                }
            }

        }
    }
}

