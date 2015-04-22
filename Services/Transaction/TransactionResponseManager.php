<?php
/*
 * This file is part of the Ecentria software.
 *
 * (c) 2014, OpticsPlanet, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\CoreRestBundle\Services\Transaction;

use Doctrine\Common\Collections\ArrayCollection;

use Ecentria\Libraries\CoreRestBundle\Entity\Transaction,
    Ecentria\Libraries\CoreRestBundle\Model\CRUD\CrudEntityInterface,
    Ecentria\Libraries\CoreRestBundle\Model\CollectionResponse,
    Ecentria\Libraries\CoreRestBundle\Services\Transaction\Handler\TransactionHandlerInterface;

use Gedmo\Exception\FeatureNotImplementedException;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * Transaction respnse manager
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class TransactionResponseManager
{
    /**
     * Handlers
     *
     * @var TransactionHandlerInterface[]
     */
    private $handlers;

    /**
     * Constructor
     *
     * @param array $handlers handlers
     */
    public function __construct(array $handlers)
    {
        $this->handlers = $handlers;
    }

    /**
     * Handle
     *
     * @param Transaction             $transaction transaction
     * @param mixed                   $data        data
     * @param ConstraintViolationList $violations  violations
     *
     * @throws FeatureNotImplementedException
     * @throws \Exception
     *
     * @return CollectionResponse|CrudEntityInterface
     */
    public function handle(Transaction $transaction, $data, ConstraintViolationList $violations = null)
    {
        if (is_array($data)) {
            $data = new ArrayCollection($data);
        }

        $handled = false;
        foreach ($this->handlers as $handler) {
            if (!$handler instanceof TransactionHandlerInterface) {
                throw new \Exception('Handler must be instance of TransactionHandlerInterface');
            }
            if ($handler->supports() === $transaction->getMethod()) {
                $data = $handler->handle($transaction, $data, $violations);
                $handled = true;
            }
        }

        if (!$handled) {
            throw new FeatureNotImplementedException(
                'Method ' . $transaction->getMethod() . ' is not supported yet.'
            );
        }

        $data->setTransaction($transaction);

        if (!$transaction->getSuccess()) {
            $data->setShowAssociations(true);
        }

        return $data;
    }
}
