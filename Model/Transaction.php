<?php
/*
 * This file is part of the ecentria group, inc. software.
 *
 * (c) 2016, ecentria group, inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\EcentriaRestBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Transaction Model
 *
 * @author Artem Petrov <artem.petrov@opticsplanet.com>
 */
class Transaction
{
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_PATCH = 'PATCH';
    const METHOD_DELETE = 'DELETE';
    const METHOD_GET = 'GET';

    const SOURCE_REST = 'REST';
    const SOURCE_CLI = 'CLI';
    const SOURCE_SERVICE = 'SERVICE';

    const STATUS_OK = 200;
    const STATUS_CREATED = 201;
    const STATUS_BAD_REQUEST = 400;
    const STATUS_NOT_FOUND = 404;
    const STATUS_CONFLICT = 409;

    /**
     * Identifier
     *
     * @var int
     */
    private $id;

    /**
     * Name of api endpoint
     *
     * @var string
     */
    private $model;

    /**
     * Related entity
     *
     * @var string
     */
    private $relatedIds;

    /**
     * Related entity
     *
     * @var string
     */
    private $relatedRoute;

    /**
     * Request method
     * (post, put, patch, delete, get)
     *
     * @var string
     */
    private $method;

    /**
     * Request source
     * (rest, cli, service)
     *
     * @var string
     */
    private $requestSource;

    /**
     * Request id
     *
     * @var string
     */
    private $requestId;

    /**
     * Created at given datetime
     *
     * @var \DateTime
     */
    private $createdAt;

    /**
     * Updated at given datetime
     *
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * Status
     * (201, 200, 400, 409)
     *
     * @var int
     */
    private $status;

    /**
     * Success
     * true < 400 >= false
     *
     * @var bool
     */
    private $success;

    /**
     * Messages
     * Json encoded messages
     *
     * @var array
     */
    private $messages = [];

    /**
     * Response time in milliseconds
     *
     * @var int
     */
    private $responseTime;

    /**
     * Request Content
     *
     * @var array
     */
    private $postContent;

    /**
     * Request Parameters
     *
     * @var array
     */
    private $parameters;

    /**
     * Id getter
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Id setter
     *
     * @param int $id
     * @return Transaction
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Model setter
     *
     * @param string $model
     *
     * @return Transaction
     */
    public function setModel($model)
    {
        $this->model = $model;
        return $this;
    }

    /**
     * Model getter
     *
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * RelatedIds setter
     *
     * @param array $relatedIds
     *
     * @return $this
     */
    public function setRelatedIds($relatedIds)
    {
        $this->relatedIds = $relatedIds;
        return $this;
    }

    /**
     * RelatedId getter
     *
     * @return array
     */
    public function getRelatedIds()
    {
        return $this->relatedIds;
    }

    /**
     * RelatedRoute setter
     *
     * @param string $relatedRoute
     *
     * @return $this
     */
    public function setRelatedRoute($relatedRoute)
    {
        $this->relatedRoute = $relatedRoute;
        return $this;
    }

    /**
     * RelatedRoute getter
     *
     * @return string
     */
    public function getRelatedRoute()
    {
        return $this->relatedRoute;
    }

    /**
     * Method setter
     *
     * @param string $method
     *
     * @return Transaction
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * Method getter
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * RequestSource setter
     *
     * @param string $requestSource
     *
     * @return Transaction
     */
    public function setRequestSource($requestSource)
    {
        $this->requestSource = $requestSource;
        return $this;
    }

    /**
     * RequestSource getter
     *
     * @return string
     */
    public function getRequestSource()
    {
        return $this->requestSource;
    }

    /**
     * RequestId setter
     *
     * @param string $requestId
     *
     * @return Transaction
     */
    public function setRequestId($requestId)
    {
        $this->requestId = $requestId;
        return $this;
    }

    /**
     * RequestId getter
     *
     * @return string
     */
    public function getRequestId()
    {
        return $this->requestId;
    }

    /**
     * CreatedAt setter
     *
     * @param \DateTime $createdAt
     *
     * @return Transaction
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * CreatedAt getter
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * UpdatedAt setter
     *
     * @param \DateTime $updatedAt
     *
     * @return Transaction
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * UpdatedAt getter
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Status setter
     *
     * @param int $status
     *
     * @return Transaction
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Status getter
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Success setter
     *
     * @param boolean $success
     *
     * @return Transaction
     */
    public function setSuccess($success)
    {
        $this->success = $success;
        return $this;
    }

    /**
     * Success getter
     *
     * @return boolean
     */
    public function getSuccess()
    {
        return $this->success;
    }

    /**
     * Messages setter
     *
     * @param ArrayCollection $messages
     *
     * @return Transaction
     */
    public function setMessages(ArrayCollection $messages)
    {
        $this->messages = $messages->toArray();
        return $this;
    }

    /**
     * Messages getter
     *
     * @return ArrayCollection
     */
    public function getMessages()
    {
        return new ArrayCollection($this->messages);
    }

    /**
     * ResponseTime setter
     *
     * @param int $milliseconds
     *
     * @return Transaction
     */
    public function setResponseTime($milliseconds)
    {
        $this->responseTime = $milliseconds;
        return $this;
    }

    /**
     * ResponseTime getter
     *
     * @return int
     */
    public function getResponseTime()
    {
        return $this->responseTime;
    }

    /**
     * Post Content setter
     *
     * @param array $postContent
     *
     * @return $this
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
}
