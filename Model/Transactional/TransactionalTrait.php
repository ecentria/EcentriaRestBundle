<?php
/*
 * This file is part of the ecentria group, inc. software.
 *
 * (c) 2015, ecentria group, inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\EcentriaRestBundle\Model\Transactional;

use Ecentria\Libraries\EcentriaRestBundle\Model\Transaction;

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
     * @param Transaction|null $transaction
     *
     * @see \Ecentria\Libraries\EcentriaRestBundle\Model\Transactional\TransactionalInterface::setTransaction
     *
     * @return TransactionalTrait
     */
    public function setTransaction(Transaction $transaction = null)
    {
        $this->transaction = $transaction;
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Ecentria\Libraries\EcentriaRestBundle\Model\Transactional\TransactionalInterface::getTransaction
     *
     * @return Transaction|null
     */
    public function getTransaction()
    {
        return $this->transaction;
    }
}
