<?php
/*
 * This file is part of the ecentria group, inc. software.
 *
 * (c) 2015, ecentria group, inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\EcentriaRestBundle\Services\Transaction;

use Ecentria\Libraries\EcentriaRestBundle\Model\Transaction;
use Ecentria\Libraries\EcentriaRestBundle\Services\UUID;

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
     * Related ids
     *
     * @var string
     */
    protected $relatedIds;

    /**
     * Post Content
     * @var array
     */
    private $postContent;

    /**
     * Get Parameters
     * @var array
     */
    private $parameters;

    /**
     * RequestMethod setter
     *
     * @param string $requestMethod
     *
     * @return TransactionBuilder
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
     * @return TransactionBuilder
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
     * @return TransactionBuilder
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
     * @return TransactionBuilder
     */
    public function setRelatedRoute($relatedRoute)
    {
        $this->relatedRoute = $relatedRoute;
        return $this;
    }

    /**
     * RelatedIds setter
     *
     * @param string $relatedIds
     *
     * @return TransactionBuilder
     */
    public function setRelatedIds($relatedIds)
    {
        $this->relatedIds = $relatedIds;
        return $this;
    }

    /**
     * RelatedId getter
     *
     * @return string
     */
    public function getRelatedIds()
    {
        return $this->relatedIds;
    }

    /**
     * Post Content setter
     *
     * @param array $postContent
     *
     * @return TransactionBuilder
     */
    public function setPostContent($postContent)
    {
        $this->postContent = $postContent;
        return $this;
    }

    /**
     * Post Content getter
     *
     * @return array
     */
    public function getPostContent()
    {
        return $this->postContent;
    }

    /**
     * Query Params setter
     *
     * @param array $parameters
     *
     * @return TransactionBuilder
     */
    public function setQueryParams($parameters)
    {
        $this->parameters = $parameters;
        return $this;
    }

    /**
     * Query Params getter
     *
     * @return array
     */
    public function getQueryParams()
    {
        return $this->parameters;
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
            ->setRelatedIds($this->relatedIds)
            ->setRequestId(microtime())
            ->setId(UUID::generate())
            ->setRequestSource($this->requestSource)
            ->setModel($this->model)
            ->setCreatedAt($datetime)
            ->setUpdatedAt($datetime)
            ->setResponseTime(0)
            ->setPostContent($this->postContent)
            ->setQueryParams($this->parameters);
        return $transaction;
    }
}
