<?php
/*
 * This file is part of the Ecentria software.
 *
 * (c) 2014, OpticsPlanet, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\CoreRestBundle\Services\Transaction\Handler;

use Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\EntityManager,
    Doctrine\ORM\UnitOfWork;

use Ecentria\Libraries\CoreRestBundle\Entity\Transaction,
    Ecentria\Libraries\CoreRestBundle\Model\CollectionResponse,
    Ecentria\Libraries\CoreRestBundle\Model\CRUD\CrudEntityInterface,
    Ecentria\Libraries\CoreRestBundle\Model\Error,
    Ecentria\Libraries\CoreRestBundle\Services\ErrorBuilder;

use Gedmo\Exception\FeatureNotImplementedException;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * Transaction GET handler
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class TransactionGetHandler implements TransactionHandlerInterface
{
    /**
     * Constructor
     *
     * @param EntityManager $entityManager entityManager
     * @param ErrorBuilder  $errorBuilder  errorBuilder
     */
    public function __construct(
        EntityManager $entityManager,
        ErrorBuilder $errorBuilder
    ) {
        $this->entityManager = $entityManager;
        $this->errorBuilder = $errorBuilder;
    }

    /**
     * Supports method
     *
     * @return string
     */
    public function supports()
    {
        return 'GET';
    }

    /**
     * Handle
     *
     * @param Transaction                         $transaction Transaction
     * @param CrudEntityInterface|ArrayCollection $data        Data
     * @param ConstraintViolationList|null        $violations  Violations
     *
     * @throws FeatureNotImplementedException
     *
     * @return CrudEntityInterface|CollectionResponse
     */
    public function handle(Transaction $transaction, $data, ConstraintViolationList $violations = null)
    {
        if ($data instanceof CrudEntityInterface) {
            $data = $this->handleEntity($transaction, $data);
        } elseif ($data instanceof ArrayCollection) {
            $data = $this->handleCollection($transaction, $data);
        } else {
            throw new FeatureNotImplementedException(
                get_class($data) . ' class is not supported by transactions (GET). Instance of CrudEntity needed.'
            );
        }
        return $data;
    }

    /**
     * Handle entity
     *
     * @param Transaction         $transaction transaction
     * @param CrudEntityInterface $entity      entity
     *
     * @return CrudEntityInterface
     */
    private function handleEntity(Transaction $transaction, CrudEntityInterface $entity)
    {
        $success = $this->isEntityManaged($entity);
        $status = $success ? Transaction::STATUS_OK : Transaction::STATUS_NOT_FOUND;

        $transaction->setStatus($status);
        $transaction->setSuccess($success);

        if (!$success) {
            $this->errorBuilder->addCustomError(
                $entity->getId(),
                new Error('Entity not found', Transaction::STATUS_NOT_FOUND, null, Error::CONTEXT_GLOBAL)
            );
            $this->errorBuilder->setTransactionErrors($transaction);
        }

        return $entity;
    }

    /**
     * Handle collection
     *
     * @param Transaction $transaction transaction
     * @param mixed       $data        data
     *
     * @return CollectionResponse
     */
    private function handleCollection(Transaction $transaction, $data)
    {
        $data = new CollectionResponse($data);

        $transaction->setStatus(Transaction::STATUS_OK);
        $transaction->setSuccess(true);

        return $data;
    }

    /**
     * Is entity managed
     *
     * @param CrudEntityInterface $entity entity
     *
     * @return bool
     */
    private function isEntityManaged(CrudEntityInterface $entity)
    {
        return UnitOfWork::STATE_MANAGED === $this->entityManager->getUnitOfWork()->getEntityState($entity);
    }
}
