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

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\UnitOfWork;
use Ecentria\Libraries\EcentriaRestBundle\Model\Transaction,
    Ecentria\Libraries\EcentriaRestBundle\Model\CRUD\CrudEntityInterface,
    Ecentria\Libraries\EcentriaRestBundle\Model\Error,
    Ecentria\Libraries\EcentriaRestBundle\Services\ErrorBuilder;

use Gedmo\Exception\FeatureNotImplementedException;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * Transaction PATCH handler
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class TransactionPatchHandler implements TransactionHandlerInterface
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
     * @param ErrorBuilder    $errorBuilder $errorBuilder
     */
    public function __construct(ManagerRegistry $registry, ErrorBuilder $errorBuilder)
    {
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
        return 'PATCH';
    }

    /**
     * Handle
     *
     * @param Transaction                  $transaction Transaction
     * @param CrudEntityInterface          $data        Data
     * @param ConstraintViolationList|null $violations  Violations
     *
     * @throws FeatureNotImplementedException
     *
     * @return CrudEntityInterface
     */
    public function handle(Transaction $transaction, $data, ConstraintViolationList $violations = null)
    {
        if (!$data instanceof CrudEntityInterface) {
            throw new FeatureNotImplementedException(
                get_class($data) . ' class is not supported by transactions (PATCH). Instance of CrudEntity needed.'
            );
        }

        if (!$this->isEntityManaged($data)) {
            $this->errorBuilder->addCustomError(
                $data->getPrimaryKey(),
                new Error('Entity not found', Transaction::STATUS_NOT_FOUND, null, Error::CONTEXT_GLOBAL)
            );
        }

        $errorCode = Transaction::STATUS_OK;
        $this->errorBuilder->processViolations($violations);
        if ($this->errorBuilder->hasErrors()) {
            $this->errorBuilder->setTransactionErrors($transaction);
            foreach ($this->errorBuilder->getErrors() as $error) {
                $errorCode = $error->getCode();
            }
        }
        $transaction->setStatus($errorCode);
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
