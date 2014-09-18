<?php
/*
 * This file is part of the Ecentria software.
 *
 * (c) 2014, OpticsPlanet, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\CoreRestBundle\Services;

use Ecentria\Libraries\CoreRestBundle\Entity\Transaction;

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
     * Build
     */
    public function build()
    {
        $datetime = new \DateTime();
        $transaction = new Transaction();
        $transaction->setMethod($this->requestMethod)
            ->setRelatedRoute($this->relatedRoute)
            ->setRequestId(microtime())
            ->setId(UUID::generate())
            ->setRequestSource($this->requestSource)
            ->setModel($this->model)
            ->setCreatedAt($datetime)
            ->setUpdatedAt($datetime);
        return $transaction;
    }
}