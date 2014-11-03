<?php
/*
 * This file is part of the Ecentria software.
 *
 * (c) 2014, OpticsPlanet, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\CoreRestBundle\Services\Transaction;

use Ecentria\Libraries\CoreRestBundle\Entity\Transaction;
use Ecentria\Libraries\CoreRestBundle\Services\UUID;

/**
 * Transaction service
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class TransactionBuilder
{
    /**
     * Request method
     *
     * @var string
     */
    protected $requestMethod;

    /**
     * Request Source
     *
     * @var string
     */
    protected $requestSource;

    /**
     * Model
     *
     * @var string
     */
    protected $model;

    /**
     * Related route (GET)
     *
     * @var string
     */
    protected $relatedRoute;

    /**
     * Related id
     *
     * @var string
     */
    protected $relatedId;

    /**
     * RequestMethod setter
     *
     * @param string $requestMethod
     *
     * @return self
     */
    public function setRequestMethod($requestMethod)
    {
        $this->requestMethod = $requestMethod;
        return $this;
    }

    /**
     * Request Source setter
     *
     * @param string $requestSource
     *
     * @return self
     */
    public function setRequestSource($requestSource)
    {
        $this->requestSource = $requestSource;
        return $this;
    }

    /**
     * Model setter
     *
     * @param string $model
     *
     * @return self
     */
    public function setModel($model)
    {
        $this->model = $model;
        return $this;
    }

    /**
     * RelatedRoute setter
     *
     * @param string $relatedRoute
     *
     * @return self
     */
    public function setRelatedRoute($relatedRoute)
    {
        $this->relatedRoute = $relatedRoute;
        return $this;
    }

    /**
     * RelatedId setter
     *
     * @param string $relatedId
     *
     * @return self
     */
    public function setRelatedId($relatedId)
    {
        $this->relatedId = $relatedId;
        return $this;
    }

    /**
     * RelatedId getter
     *
     * @return string
     */
    public function getRelatedId()
    {
        return $this->relatedId;
    }

    /**
     * Building transaction
     *
     * @return Transaction
     */
    public function build()
    {
        $datetime = new \DateTime();
        $transaction = new Transaction();
        $transaction->setMethod($this->requestMethod)
            ->setRelatedRoute($this->relatedRoute)
            ->setRelatedId($this->relatedId)
            ->setRequestId(microtime())
            ->setId(UUID::generate())
            ->setRequestSource($this->requestSource)
            ->setModel($this->model)
            ->setCreatedAt($datetime)
            ->setUpdatedAt($datetime);
        return $transaction;
    }
}