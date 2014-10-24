<?php
/*
 * This file is part of the Ecentria software.
 *
 * (c) 2014, OpticsPlanet, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\CoreRestBundle\Traits;

use Ecentria\Libraries\CoreRestBundle\Entity\Transaction;

/**
 * Transactional trait
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
trait TransactionalTrait
{
    /**
     * Transaction
     * We don't store this transaction.
     * May be in future we'll find a way to correct storing of all transactions
     * May be not.
     *
     * @var Transaction|null
     */
    protected $transaction = null;

    /**
     * {@inheritdoc}
     *
     * @see \Ecentria\Libraries\CoreRestBundle\Interfaces\TransactionalInterface::setTransaction
     */
    public function setTransaction(Transaction $transaction = null)
    {
        $this->transaction = $transaction;
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Ecentria\Libraries\CoreRestBundle\Interfaces\TransactionalInterface::getTransaction
     */
    public function getTransaction()
    {
        return $this->transaction;
    }
}