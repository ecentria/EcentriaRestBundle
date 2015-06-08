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
    Ecentria\Libraries\CoreRestBundle\Model\CRUD\CrudEntityInterface,
    Ecentria\Libraries\CoreRestBundle\Services\ErrorBuilder;

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
     * Constructor
     *
     * @param ErrorBuilder $errorBuilder $errorBuilder
     */
    public function __construct(ErrorBuilder $errorBuilder)
    {
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

        $this->errorBuilder->processViolations($violations);
        $this->errorBuilder->setTransactionErrors($transaction);

        $success = !$this->errorBuilder->hasErrors();
        $status = $success ? Transaction::STATUS_OK : Transaction::STATUS_CONFLICT;

        $transaction->setStatus($status);
        $transaction->setSuccess($success);

        return $data;
    }
}
