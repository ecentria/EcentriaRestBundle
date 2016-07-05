<?php
/**
 * Created by PhpStorm.
 * User: artem.petrov
 * Date: 7/1/16
 * Time: 2:24 PM
 */

namespace Ecentria\Libraries\EcentriaRestBundle\Services\Transaction\Storage;

use Ecentria\Libraries\EcentriaRestBundle\Model\Transaction;


interface TransactionStorageInterface {

    public function read($id);

    public function write(Transaction $transaction);
} 