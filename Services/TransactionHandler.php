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
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\UnitOfWork;
use Ecentria\Libraries\CoreRestBundle\Entity\CRUDEntity;
use Ecentria\Libraries\CoreRestBundle\Entity\Transaction;
use Ecentria\Libraries\CoreRestBundle\Model\CollectionResponse;
use Ecentria\Libraries\CoreRestBundle\Model\Error;
use Gedmo\Exception\FeatureNotImplementedException;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * Transaction service
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class TransactionHandler
{
    /**
     * Entity manager
     *
     * @var EntityManager
     */
    private $entityManager;

    /**
     * Transaction
     *
     * @var Transaction
     */
    private $transaction;

    /**
     * Notice builder
     *
     * @var NoticeBuilder
     */
    private $noticeBuilder;

    /**
     * Error builder
     *
     * @var ErrorBuilder
     */
    private $errorBuilder;

    /**
     * Data
     *
     * @var ArrayCollection|CRUDEntity[]|CRUDEntity
     */
    private $data;

    /**
     * Violations
     *
     * @var ConstraintViolationList|null
     */
    private $violations;

    /**
     * @param EntityManager $entityManager
     * @param ErrorBuilder $errorBuilder
     * @param NoticeBuilder $noticeBuilder
     */
    public function __construct(
        EntityManager $entityManager,
        ErrorBuilder $errorBuilder,
        NoticeBuilder $noticeBuilder
    ) {
        $this->entityManager = $entityManager;
        $this->errorBuilder = $errorBuilder;
        $this->noticeBuilder = $noticeBuilder;
    }

    /**
     * Handle
     *
     * @param Transaction $transaction
     * @param ArrayCollection|CrudEntity $data
     * @param ConstraintViolationList $violations
     * @throws FeatureNotImplementedException
     * @return CollectionResponse|CrudEntity
     */
    public function handle(Transaction $transaction, $data, ConstraintViolationList $violations = null)
    {
        $this->transaction = $transaction;
        $this->data = $data;
        $this->violations = $violations;

        $method = $this->transaction->getMethod();

        switch ($method) {
            case Transaction::METHOD_GET:
                $this->handleGet();
                break;
            case Transaction::METHOD_PATCH:
                $this->handlePatch();
                break;
            case Transaction::METHOD_POST:
                $this->handlePost();
                break;
            default:
                throw new FeatureNotImplementedException($method . ' request method is not supported yet.');
                break;
        }

        $this->entityManager->persist($this->transaction);

        return $this->data;
    }

    /**
     * Handling GET method
     */
    private function handleGet()
    {
        if (!$this->data instanceof CRUDEntity) {
            throw new FeatureNotImplementedException(
                get_class($this->data) . ' class is not supported by transactions. Instance of CRUDEntity needed.'
            );
        }

        if ($this->isEntityManaged($this->data)) {
            $this->transaction->setStatus(Transaction::STATUS_OK);
            $this->transaction->setSuccess(true);
            $this->data->setShowAssociations(true);
        } else {
            $this->transaction->setStatus(Transaction::STATUS_NOT_FOUND);
            $this->transaction->setSuccess(false);
            $this->errorBuilder->addCustomError(
                $this->data->getId(),
                new Error('Entity not found', Transaction::STATUS_NOT_FOUND, null, Error::CONTEXT_GLOBAL)
            );
            $this->setErrors($this->transaction);
        }
        $this->transaction->setRelatedId($this->data->getId());
        $this->data->setTransaction($this->transaction);
    }

    /**
     * Handling PATCH method
     */
    private function handlePatch()
    {
        if (!$this->data instanceof CRUDEntity) {
            throw new FeatureNotImplementedException(
                get_class($this->data) . ' class is not supported by transactions. Instance of CRUDEntity needed.'
            );
        }
        $this->errorBuilder->processViolations($this->violations);
        if (!$this->errorBuilder->hasErrors()) {
            $this->transaction->setStatus(Transaction::STATUS_OK);
            $this->transaction->setSuccess(true);
            $this->data->setShowAssociations(true);
        } else {
            $this->transaction->setStatus(Transaction::STATUS_CONFLICT);
            $this->transaction->setSuccess(false);
            $this->data->setIsEmbedded(true);
            $this->setErrors($this->transaction);
        }
        $this->transaction->setRelatedId($this->data->getId());
        $this->data->setTransaction($this->transaction);
    }

    /**
     * Handling PATCH method
     */
    private function handlePost()
    {
        $this->errorBuilder->processViolations($this->violations);

        if (!$this->errorBuilder->hasErrors()) {
            $this->transaction->setStatus(Transaction::STATUS_CREATED);
            $this->transaction->setSuccess(true);
        } else {
            $this->transaction->setStatus(Transaction::STATUS_CONFLICT);
            $this->transaction->setSuccess(false);
            $this->setErrors($this->transaction);
        }
        if ($this->data instanceof ArrayCollection) {
            $this->handleCollection();
            $this->setNotices($this->transaction);
            $this->data = new CollectionResponse($this->data);
            $this->data->setTransaction($this->transaction);
        } else {
            throw new FeatureNotImplementedException(
                'Handling POST method with single CRUDEntity is not supported. Use collection instead'
            );
        }
    }

    /**
     * Handle collection
     */
    private function handleCollection()
    {
        foreach ($this->data as $entity) {

            $transaction = clone $this->transaction;
            $transaction->setRequestSource(Transaction::SOURCE_SERVICE);
            $transaction->setId(UUID::generate());
            $transaction->setRequestId(microtime());
            $transaction->setRelatedId($entity->getId());

            $errors = $this->errorBuilder->getEntityErrors($entity->getId());
            $messages = new ArrayCollection();

            if ($errors->isEmpty()) {
                $transaction->setSuccess(true);
                $transaction->setStatus(Transaction::STATUS_CREATED);
                $entity->setIsEmbedded(false);
                $entity->setShowAssociations(true);
                $this->noticeBuilder->addSuccess();
            } else {
                $messages->set('errors', $errors);
                $transaction->setSuccess(false);
                $transaction->setStatus(Transaction::STATUS_CONFLICT);
                $this->noticeBuilder->addFail();
            }
            $transaction->setMessages($messages);
            $this->entityManager->persist($transaction);
            $entity->setTransaction($transaction);
        }
    }

    /**
     * Setting errors only
     *
     * @param Transaction $transaction
     */
    private function setErrors(Transaction $transaction)
    {
        $messages = new ArrayCollection();
        $globalErrors = $this->errorBuilder->getErrors(Error::CONTEXT_GLOBAL);
        if (!$globalErrors->isEmpty()) {
            $transaction->setStatus(Transaction::STATUS_NOT_FOUND);
        }
        $errors = $this->errorBuilder->getErrors();
        if (!$errors->isEmpty()) {
            $messages->set('errors', $errors);
        }
        $transaction->setMessages($messages);
    }

    /**
     * Setting errors only
     *
     * @param Transaction $transaction
     */
    private function setNotices(Transaction $transaction)
    {
        $messages = $transaction->getMessages();
        if (is_null($messages)) {
            $messages = new ArrayCollection();
        }
        if (!$this->noticeBuilder->isEmpty()) {
            $messages->set('notices', $this->noticeBuilder->getNotices());
        }
        $transaction->setMessages($messages);
    }

    /**
     * Is entity managed
     * @param CRUDEntity $entity
     * @return bool
     */
    private function isEntityManaged(CRUDEntity $entity)
    {
        return UnitOfWork::STATE_MANAGED === $this->entityManager->getUnitOfWork()->getEntityState($entity);
    }
}