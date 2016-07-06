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


interface TransactionStorageInterface {

    /**
     * Read Transaction Model from Transaction Storage
     *
     * @param $id
     * @return Transaction
     */
    public function read($id);

    /**
     * Write Transaction Model to Transaction Storage
     *
     * @param Transaction $transaction
     * @return void
     */
    public function write(Transaction $transaction);
} 