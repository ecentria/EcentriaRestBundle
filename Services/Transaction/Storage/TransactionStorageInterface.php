<?php
/*
 * This file is part of the ecentria group, inc. software.
 *
 * (c) 2016, ecentria group, inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\EcentriaRestBundle\Services\Transaction\Storage;

use Ecentria\Libraries\EcentriaRestBundle\Model\Transaction;

/**
 * Transaction Storage Interface
 *
 * @author Artem Petrov <artem.petrov@opticsplanet.com>
 */
interface TransactionStorageInterface {

    /**
     * Read Transaction Model from Transaction Storage
     *
     * @param $id
     * @return Transaction
     */
    public function read($id);

    /**
     * Tells the Transaction Storage to make an model persistent
     * The model will be entered into the storage as a result of the write operation.
     *
     * @param Transaction $transaction
     * @return void
     */
    public function persist(Transaction $transaction);

    /**
     * Writes all models that have been queued up to now to the storage
     *
     * @return void
     */
    public function write();
}
