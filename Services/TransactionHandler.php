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
use Ecentria\Libraries\CoreRestBundle\Entity\NullEntity;
use Ecentria\Libraries\CoreRestBundle\Entity\Transaction;
use Ecentria\Libraries\CoreRestBundle\Model\Error;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * Transaction service
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class TransactionHandler
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var Transaction
     */
    private $transaction;

    /**
     * @var ArrayCollection
     */
    private $errors;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->errors = new ArrayCollection();
    }

    /**
     * Handle
     *
     * @param Transaction $transaction
     * @param $data
     * @param ConstraintViolationList $violations
     * @return Transaction
     */
    public function handle(Transaction $transaction, $data, ConstraintViolationList $violations = null)
    {
        $this->transaction = $transaction;

        if ($data instanceof ArrayCollection) {
            $this->handleCollection($data, $violations);
        } else {
            $this->handleEntity($data, $violations);
        }

        $this->transaction->setMessages($this->getMessages());

        $this->entityManager->persist($this->transaction);
        $this->entityManager->flush($this->transaction);

        if ($this->transaction->getSuccess()) {
            if ($data instanceof ArrayCollection) {
                $data = $data->toArray();
            }
        } else {
            $data = new NullEntity();
            $data->setTransaction($this->transaction);
        }

        return $data;
    }

    /**
     * Handle collection
     *
     * @param ArrayCollection $data
     * @param ConstraintViolationList $violations
     */
    private function handleCollection(ArrayCollection $data, ConstraintViolationList $violations = null)
    {
        if ($data->count() === 1) {
            $data = $data->first();
            $this->handleEntity($data, $violations);
        }

        foreach ($data as $item) {
            if ($item instanceof CRUDEntity) {
                $item->setTransaction($this->transaction);
            }
        }
        if ($this->transaction->getMethod() === Transaction::METHOD_POST) {
            $this->handlePost($violations);
        }
    }

    /**
     * @param CRUDEntity $entity
     * @param ConstraintViolationList $violations
     *
     * @return Transaction
     */
    private function handleEntity(CRUDEntity $entity, ConstraintViolationList $violations = null)
    {
        $this->transaction->setRelatedId($entity->getId());
        if ($this->transaction->getMethod() === Transaction::METHOD_GET) {
            $this->handleGet($entity);
        }
        if ($this->transaction->getMethod() === Transaction::METHOD_PATCH) {
            $this->handlePatch($violations);
        }
        $entity->setTransaction($this->transaction);
    }

    /**
     * Handling PATCH method
     *
     * @param ConstraintViolationList|ConstraintViolation[] $violations
     */
    private function handlePost(ConstraintViolationList $violations = null)
    {
        if (is_null($violations)) {
            $this->transaction->setStatus(Transaction::STATUS_CREATED);
            $this->transaction->setSuccess(true);
        } else {
            $this->transaction->setSuccess(false);
            $this->transaction->setStatus(Transaction::STATUS_CONFLICT);
            foreach ($violations as $violation) {
                $code = $violation->getCode();
                if ($code && $this->transaction->getStatus() !== $code) {
                    $this->transaction->setStatus($code);
                }
                $context =
                    $violation->getCode() === Transaction::STATUS_NOT_FOUND ?
                        Error::CONTEXT_GLOBAL :
                        Error::CONTEXT_DATA;

                $this->errors->add(
                    new Error(
                        $violation->getMessage(),
                        $violation->getCode(),
                        $context,
                        $violation->getPropertyPath()
                    )
                );
            }
        }
    }

    /**
     * Handling PATCH method
     *
     * @param ConstraintViolationList|ConstraintViolation[] $violations
     */
    private function handlePatch(ConstraintViolationList $violations = null)
    {
        if (is_null($violations)) {
            $this->transaction->setStatus(Transaction::STATUS_OK);
            $this->transaction->setSuccess(true);
        } else {
            $this->transaction->setSuccess(false);
            $this->transaction->setStatus(Transaction::STATUS_CONFLICT);
            foreach ($violations as $violation) {
                $code = $violation->getCode();
                if ($code && $this->transaction->getStatus() !== $code) {
                    $this->transaction->setStatus($code);
                }
                $context =
                    $violation->getCode() === Transaction::STATUS_NOT_FOUND ?
                        Error::CONTEXT_GLOBAL :
                        Error::CONTEXT_DATA;

                $this->errors->add(
                    new Error(
                        $violation->getMessage(),
                        $violation->getCode(),
                        $context,
                        $violation->getPropertyPath()
                    )
                );
            }
        }
    }

    /**
     * Handling GET method
     *
     * @param CRUDEntity $entity
     */
    private function handleGet(CRUDEntity $entity)
    {
        if ($this->isEntityManaged($entity)) {
            $this->transaction->setStatus(Transaction::STATUS_OK);
            $this->transaction->setSuccess(true);
        } else {
            $this->transaction->setStatus(Transaction::STATUS_NOT_FOUND);
            $this->transaction->setSuccess(false);
            $this->errors->add(
                new Error(
                    'Not found',
                    Transaction::STATUS_NOT_FOUND,
                    Error::CONTEXT_GLOBAL
                )
            );
        }
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

    /**
     * Get messages
     *
     * @return ArrayCollection
     */
    private function getMessages()
    {
        $messages = new ArrayCollection();
        if (!$this->errors->isEmpty()) {
            $messages->set('errors', $this->errors);
        }
        return $messages->isEmpty() ? null : $messages;
    }
}