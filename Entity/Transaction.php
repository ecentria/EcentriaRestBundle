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
use Symfony\Component\Validator\Constraints as Assert;
use Ecentria\Libraries\CoreRestBundle\Validator\Constraints as EcentriaAssert;


/**
 * Transaction entity
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 *
 * @ORM\Entity()
 * @ORM\Table(name="transaction")
 * @EcentriaAssert\TransactionSuccess()
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
    const STATUS_CONFLICT = 409;

    /**
     * Identifier
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="transaction_id", type="bigint")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Name of api endpoint
     *
     * @var string
     *
     * @ORM\Column(name="model", type="string", nullable=false)
     *
     * @Assert\NotNull()
     */
    private $model;

    /**
     * Request method
     * (post, put, patch, delete, get)
     *
     * @var string
     *
     * @ORM\Column(name="method", type="string", length=7, nullable=false)
     *
     * @Assert\NotNull()
     *
     * @EcentriaAssert\InArray(
     *      values={
     *          Transaction::METHOD_POST,
     *          Transaction::METHOD_PUT,
     *          Transaction::METHOD_PATCH,
     *          Transaction::METHOD_DELETE,
     *          Transaction::METHOD_GET
     *      }
     * )
     */
    private $method;

    /**
     * Request source
     * (rest, cli, service)
     *
     * @var string
     *
     * @ORM\Column(name="request_source", type="string", nullable=false)
     *
     * @Assert\NotNull()
     *
     * @EcentriaAssert\InArray(
     *      values={
     *          Transaction::SOURCE_REST,
     *          Transaction::SOURCE_CLI,
     *          Transaction::SOURCE_SERVICE
     *      }
     * )
     */
    private $requestSource;

    /**
     * Request id
     *
     * @var string
     *
     * @ORM\Column(name="request_id", type="string", nullable=false)
     *
     * @Assert\NotNull()
     */
    private $requestId;

    /**
     * Created at given datetime
     *
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     *
     * @Assert\NotNull()
     *
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * Updated at given datetime
     *
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=false)
     *
     * @Assert\NotNull()
     *
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;

    /**
     * Status
     * (201, 200, 400, 409)
     *
     * @var int
     *
     * @ORM\Column(name="status", type="smallint", length=4, nullable=false)
     *
     * @Assert\NotNull()
     *
     * @EcentriaAssert\InArray(
     *      values={
     *          Transaction::STATUS_OK,
     *          Transaction::STATUS_CREATED,
     *          Transaction::STATUS_BAD_REQUEST,
     *          Transaction::STATUS_CONFLICT
     *      }
     * )
     */
    private $status;

    /**
     * Success
     * true < 400 >= false
     *
     * @var bool
     *
     * @ORM\Column(name="success", type="boolean")
     *
     * @Assert\NotNull()
     */
    private $success;

    /**
     * Message
     * Json encoded message
     *
     * @var string
     *
     * @ORM\Column(name="message", type="text")
     */
    private $messages;

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
     * Model getter
     *
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Method setter
     *
     * @param string $method
     *
     * @return self
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
     * @return self
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
     * @return self
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
     * @return self
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
     * @return self
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
     * @return self
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
     * @return self
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
     * @param string $messages
     *
     * @return self
     */
    public function setMessages($messages)
    {
        $this->messages = $messages;
        return $this;
    }

    /**
     * Messages getter
     *
     * @return string
     */
    public function getMessages()
    {
        return $this->messages;
    }
}
