<?php
/*
 * This file is part of the Ecentria software.
 *
 * (c) 2014, OpticsPlanet, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\CoreRestBundle\Model\Transactional;

use Ecentria\Libraries\CoreRestBundle\Entity\Transaction;

/**
 * Transactional interface
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
interface TransactionalInterface
{
    /**
     * Transaction setter
     *
     * @param Transaction|null $transaction
     *
     * @return self
     */
    public function setTransaction(Transaction $transaction = null);

    /**
     * Transaction getter
     *
     * @return Transaction|null
     */
    public function getTransaction();
}