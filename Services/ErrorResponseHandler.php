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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityNotFoundException;
use Ecentria\Libraries\CoreRestBundle\Model\Error;
use JMS\Serializer\Exception\ValidationFailedException;

/**
 * Error response handler
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class ErrorResponseHandler
{
    /**
     * Data
     *
     * @var mixed
     */
    private $data;

    /**
     * Status code
     *
     * @var int
     */
    private $statusCode;

    /**
     * Data setter
     *
     * @param mixed $data
     *
     * @return self
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Data getter
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * StatusCode setter
     *
     * @param int $statusCode
     *
     * @return self
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * StatusCode getter
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Handle
     *
     * @param \Exception|null $data
     */
    public function handle(\Exception $data = null) {
        if ($data instanceof \Exception) {
            $this->handleException($data);
        } elseif (is_null($data)) {
            $this->handleNull();
        }
    }

    /**
     * Handle validation exception
     *
     * @param ValidationFailedException $data
     */
    public function handleValidationException(ValidationFailedException $data)
    {
        $errors = new ArrayCollection();
        foreach ($data->getConstraintViolationList() as $constraint) {
            $errors->add($constraint);
        }
        $error = new Error('Validation failed.', Error::CODE_BAD_REQUEST);
        $error->setErrors($errors);
        $this->setData($error);
        $this->setStatusCode(Error::CODE_BAD_REQUEST);
    }

    /**
     * Handle database exception
     *
     * @param DBALException $data
     */
    public function handleDatabaseException(DBALException $data)
    {
        $error = new Error('Database conflict.', Error::CODE_CONFLICT);
        $error->setErrors(
            new ArrayCollection(
                array(
                    'message' => $data->getMessage(),
                    'file' => $data->getFile(),
                    'code' => $data->getCode()
                )
            )
        );
        $this->setData($error);
        $this->setStatusCode(Error::CODE_CONFLICT);
    }

    /**
     * Handle entity not found exception
     *
     * @param EntityNotFoundException $data
     */
    public function handleEntityNotFoundException(EntityNotFoundException $data)
    {
        $error = new Error($data->getMessage(), Error::CODE_NOT_FOUND);
        $this->setData($error);
        $this->setStatusCode(Error::CODE_NOT_FOUND);
    }

    /**
     * Handle default exception
     *
     * @param \Exception $data
     */
    public function handleDefaultException(\Exception $data)
    {
        $error = new Error($data->getMessage(), Error::CODE_BAD_REQUEST);
        $this->setData($error);
        $this->setStatusCode(Error::CODE_BAD_REQUEST);
    }

    /**
     * Exception handling controller
     *
     * @param \Exception $data
     */
    public function handleException(\Exception $data)
    {
        if ($data instanceof ValidationFailedException) {
            $this->handleValidationException($data);
        } elseif ($data instanceof DBALException) {
            $this->handleDatabaseException($data);
        } elseif ($data instanceof EntityNotFoundException) {
            $this->handleEntityNotFoundException($data);
        } elseif ($data instanceof \Exception) {
            $this->handleDefaultException($data);
        }
    }

    /**
     * Handle null
     */
    public function handleNull()
    {
        $this->setData(new Error("Not found", Error::CODE_NOT_FOUND));
        $this->setStatusCode(Error::CODE_NOT_FOUND);
    }
}