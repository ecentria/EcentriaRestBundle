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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\UnitOfWork;
use Ecentria\Libraries\CoreRestBundle\Entity\Transaction;
use Ecentria\Libraries\CoreRestBundle\Model\CollectionResponse;
use Ecentria\Libraries\CoreRestBundle\Model\CRUD\CRUDEntityInterface;
use Ecentria\Libraries\CoreRestBundle\Model\Error;
use Ecentria\Libraries\CoreRestBundle\Services\ErrorBuilder;
use Gedmo\Exception\FeatureNotImplementedException;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * Transaction service
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class TransactionGetHandler implements TransactionHandlerInterface
{
    /**
     * Constructor
     *
     * @param EntityManager $entityManager
     * @param ErrorBuilder $errorBuilder
     */
    public function __construct(
        EntityManager $entityManager,
        ErrorBuilder $errorBuilder
    ) {
        $this->entityManager = $entityManager;
        $this->errorBuilder = $errorBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function supports()
    {
        return 'GET';
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Transaction $transaction, $data, ConstraintViolationList $violations = null)
    {
        if ($data instanceof CRUDEntityInterface) {
            $data = $this->handleEntity($transaction, $data);
        } elseif ($data instanceof ArrayCollection) {
            $data = $this->handleCollection($transaction, $data);
        } else {
            throw new FeatureNotImplementedException(
                get_class($data) . ' class is not supported by transactions. Instance of CRUDEntity needed.'
            );
        }
        return $data;
    }

    /**
     * Handle entity
     */
    private function handleEntity(Transaction $transaction, CRUDEntityInterface $data)
    {
        $success = $this->isEntityManaged($data);
        $status = $success ? Transaction::STATUS_OK : Transaction::STATUS_NOT_FOUND;

        $transaction->setStatus($status);
        $transaction->setSuccess($success);

        if (!$success) {
            $this->errorBuilder->addCustomError(
                $data->getId(),
                new Error('Entity not found', Transaction::STATUS_NOT_FOUND, null, Error::CONTEXT_GLOBAL)
            );
            $this->errorBuilder->setTransactionErrors($transaction);
        }

        return $data;
    }

    /**
     * Handle collection
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
     * @param CRUDEntityInterface $entity
     * @return bool
     */
    private function isEntityManaged(CRUDEntityInterface $entity)
    {
        return UnitOfWork::STATE_MANAGED === $this->entityManager->getUnitOfWork()->getEntityState($entity);
    }
}