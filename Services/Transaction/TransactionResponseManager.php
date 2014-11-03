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
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\UnitOfWork;
use Ecentria\Libraries\CoreRestBundle\Entity\Transaction;
use Ecentria\Libraries\CoreRestBundle\Model\CRUD\CRUDEntityInterface;
use Ecentria\Libraries\CoreRestBundle\Model\CollectionResponse;
use Ecentria\Libraries\CoreRestBundle\Model\Error;
use Ecentria\Libraries\CoreRestBundle\Services\ErrorBuilder;
use Ecentria\Libraries\CoreRestBundle\Services\NoticeBuilder;
use Ecentria\Libraries\CoreRestBundle\Services\Transaction\Handler\TransactionHandlerInterface;
use Ecentria\Libraries\CoreRestBundle\Services\UUID;
use Gedmo\Exception\FeatureNotImplementedException;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * Transaction service
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class TransactionResponseManager
{
    /**
     * @var array|TransactionHandlerInterface[]
     */
    private $handlers;

    /**
     * Constructor
     *
     * @param array $handlers
     */
    public function __construct(array $handlers = array()) {
        $this->handlers = $handlers;
    }

    /**
     * Handle
     *
     * @param Transaction $transaction
     * @param $data
     * @param ConstraintViolationList $violations
     * @return CollectionResponse|CRUDEntityInterface
     */
    public function handle(Transaction $transaction, $data, ConstraintViolationList $violations = null)
    {
        if (is_array($data)) {
            $data = new ArrayCollection($data);
        }

        foreach ($this->handlers as $handler) {
            if ($handler->supports() === $transaction->getMethod()) {
                $data = $handler->handle($transaction, $data, $violations);
            }
        }

        $data->setTransaction($transaction);

        if (!$transaction->getSuccess()) {
            $data->setShowAssociations(true);
        }

        return $data;
    }
}