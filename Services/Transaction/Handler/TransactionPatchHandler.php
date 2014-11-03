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

use Ecentria\Libraries\CoreRestBundle\Entity\Transaction,
    Ecentria\Libraries\CoreRestBundle\Model\CRUD\CRUDEntityInterface,
    Ecentria\Libraries\CoreRestBundle\Services\ErrorBuilder;

use Gedmo\Exception\FeatureNotImplementedException;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * Transaction service
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class TransactionPatchHandler implements TransactionHandlerInterface
{
    /**
     * Constructor
     *
     * @param ErrorBuilder $errorBuilder
     */
    public function __construct(ErrorBuilder $errorBuilder) {
        $this->errorBuilder = $errorBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function supports()
    {
        return 'PATCH';
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Transaction $transaction, $data, ConstraintViolationList $violations = null)
    {
        if (!$data instanceof CRUDEntityInterface) {
            throw new FeatureNotImplementedException(
                get_class($data) . ' class is not supported by transactions. Instance of CRUDEntity needed.'
            );
        }
        
        $this->errorBuilder->processViolations($violations);
        $this->errorBuilder->setTransactionErrors($transaction);

        $success = !$this->errorBuilder->hasErrors();
        $status = $success ? Transaction::STATUS_OK : Transaction::STATUS_CONFLICT;

        $transaction->setStatus($status);
        $transaction->setSuccess($success);

        return $data;
    }
}