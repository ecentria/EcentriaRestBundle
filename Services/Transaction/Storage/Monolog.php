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
 * Monolog Transaction Storage
 *
 * @author Artem Petrov <artem.petrov@opticsplanet.com>
 */
class Monolog implements TransactionStorageInterface {

    /**
     * {@inheritDoc}
     */
    public function read($id)
    {
        // a stub
    }

    /**
     * {@inheritDoc}
     */
    public function persist(Transaction $transaction)
    {
        // a stub
    }

    /**
     * {@inheritDoc}
     */
    public function write()
    {
        // a stub
    }
}
