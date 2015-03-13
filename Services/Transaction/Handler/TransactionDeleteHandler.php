<?php
// @codingStandardsIgnoreFile - until DO-5738 is resolved!
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
    Doctrine\ORM\EntityManager;

use Ecentria\Libraries\CoreRestBundle\Entity\Transaction,
    Ecentria\Libraries\CoreRestBundle\Model\CollectionResponse,
    Ecentria\Libraries\CoreRestBundle\Model\CRUD\CRUDEntityInterface,
    Ecentria\Libraries\CoreRestBundle\Services\ErrorBuilder,
    Ecentria\Libraries\CoreRestBundle\Services\NoticeBuilder,
    Ecentria\Libraries\CoreRestBundle\Services\UUID;

use Gedmo\Exception\FeatureNotImplementedException;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * Transaction service
 *
 * @author Alex Niedre <alex.niedre@intexsys.lv>
 */
class TransactionDeleteHandler implements TransactionHandlerInterface
{
    /**
     * Constructor
     *
     * @param ErrorBuilder $errorBuilder Error builder
     */
    public function __construct(ErrorBuilder $errorBuilder)
    {
        $this->errorBuilder = $errorBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function supports()
    {
        return 'DELETE';
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Transaction $transaction, $data, ConstraintViolationList $violations = null)
    {
        if (!$data instanceof CRUDEntityInterface) {
            throw new FeatureNotImplementedException(
                get_class($data) . ' class is not supported by transactions (DELETE). Instance of CRUDEntity needed.'
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
