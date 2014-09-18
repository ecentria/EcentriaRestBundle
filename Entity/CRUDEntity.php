<?php
/*
 * This file is part of the Ecentria software.
 *
 * (c) 2014, OpticsPlanet, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\CoreRestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping\MappedSuperclass;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;

/**
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
 *      )
 * )
 *
 * @Hateoas\Relation(
 *      "service-transaction",
 *      exclusion = @Hateoas\Exclusion(
 *          excludeIf = "expr(object.getTransaction() === null || object.getEmbedded() === false)"
 *      ),
 *      embedded = "expr(object.getTransaction())"
 * )
 *
 * @Hateoas\Relation(
 *      "self",
 *      exclusion = @Hateoas\Exclusion(
 *          excludeIf = "expr(object.getTransaction() === null || !object.getId())"
 *      ),
 *      href = @Hateoas\Route(
 *          "expr(object.getTransaction().getRelatedRoute())",
 *          parameters = {
 *              "id" = "expr(object.getId())"
 *          },
 *          absolute = true
 *      )
 * )
 *
 * Abstract CRUD entity class
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 *
 * @MappedSuperclass
 */
abstract class CRUDEntity implements CRUDEntityInterface
{
    /**
     * Created at given datetime
     *
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    protected $createdAt;

    /**
     * Updated at given datetime
     *
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    protected $updatedAt;

    /**
     * Transaction
     * We don't store this transaction.
     * May be in future we'll find a way to correct storing of all transactions
     * May be not.
     *
     * @var null|Transaction
     * @Serializer\Exclude
     */
    protected $transaction = null;

    /**
     * Embedded?
     *
     * @var bool|null
     * @Serializer\Exclude
     */
    protected $embedded = null;

    /**
     * Associations?
     *
     * @var bool|null
     * @Serializer\Exclude
     */
    protected $showAssociations = null;

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setTransaction($transaction)
    {
        $this->transaction = $transaction;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * {@inheritdoc}
     */
    abstract public function getId();

    /**
     * {@inheritdoc}
     */
    abstract public function setId($id);

    /**
     * Embedded setter
     *
     * @param boolean $embedded
     *
     * @return $this
     */
    public function setEmbedded($embedded)
    {
        $this->embedded = (bool) $embedded;
        return $this;
    }

    /**
     * Embedded getter
     *
     * @return boolean
     */
    public function getEmbedded()
    {
        return $this->embedded;
    }

    /**
     * ShowAssociations setter
     *
     * @param bool|null $showAssociations
     *
     * @return $this
     */
    public function setShowAssociations($showAssociations)
    {
        $this->showAssociations = $showAssociations;
        return $this;
    }

    /**
     * ShowAssociations getter
     *
     * @return bool|null
     */
    public function showAssociations()
    {
        return $this->showAssociations;
    }
}
