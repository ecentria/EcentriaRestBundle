<?php
/*
 * This file is part of the ecentria group, inc. software.
 *
 * (c) 2015, ecentria group, inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\EcentriaRestBundle\Services\Transaction\Handler;

use Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\UnitOfWork;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\PersistentCollection;
use Ecentria\Libraries\EcentriaRestBundle\Model\Transaction,
    Ecentria\Libraries\EcentriaRestBundle\Model\CollectionResponse,
    Ecentria\Libraries\EcentriaRestBundle\Model\CRUD\CrudEntityInterface,
    Ecentria\Libraries\EcentriaRestBundle\Model\Error,
    Ecentria\Libraries\EcentriaRestBundle\Services\ErrorBuilder;

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
     * Registry
     *
     * @var ManagerRegistry
     */
    private $registry;

    /**
     * Error Builder
     *
     * @var ErrorBuilder
     */
    private $errorBuilder;

    /**
     * Constructor
     *
     * @param ManagerRegistry $registry     Manager Registry
     * @param ErrorBuilder    $errorBuilder errorBuilder
     */
    public function __construct(
        ManagerRegistry $registry,
        ErrorBuilder $errorBuilder
    ) {
        $this->registry = $registry;
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
        } elseif ($data instanceof PersistentCollection) {
            $data = $this->handleCollection($transaction, new ArrayCollection($data->toArray()));
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
        if (!$this->isEntityManaged($entity)) {
            $this->errorBuilder->addCustomError(
                $entity->getPrimaryKey(),
                new Error('Entity not found', Transaction::STATUS_NOT_FOUND, null, Error::CONTEXT_GLOBAL)
            );

        }

        if ($this->errorBuilder->hasErrors()) {
            $this->errorBuilder->setTransactionErrors($transaction);
            foreach ($this->errorBuilder->getErrors() as $error) {
                $errorCode = $error->getCode();
            }
        }

        $status = !$this->errorBuilder->hasErrors() ? Transaction::STATUS_OK : $errorCode;

        $transaction->setStatus($status);
        $transaction->setSuccess(!$this->errorBuilder->hasErrors());

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

        if ($this->errorBuilder->hasErrors()) {
            $this->errorBuilder->setTransactionErrors($transaction);
            foreach ($this->errorBuilder->getErrors() as $error) {
                $errorCode = $error->getCode();
            }
        }

        $status = !$this->errorBuilder->hasErrors() ? Transaction::STATUS_OK : $errorCode;

        $transaction->setStatus($status);
        $transaction->setSuccess(!$this->errorBuilder->hasErrors());

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
        $em = $this->registry->getManagerForClass(get_class($entity));
        return UnitOfWork::STATE_MANAGED === $em->getUnitOfWork()->getEntityState($entity);
    }
}
