<?php
/*
 * This file is part of the Ecentria software.
 *
 * (c) 2014, OpticsPlanet, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\CoreRestBundle\Entity;

use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;

/**
 * @Hateoas\Relation(
 *      "service-transaction",
 *      href = @Hateoas\Route(
 *          "get_transaction",
 *          parameters = {
 *              "id" = "expr(object.getTransaction().getId())"
 *          },
 *          absolute = true
 *      ),
 *      embedded = "expr(object.getTransaction())"
 * )
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class NullEntity
{
    /**
     * @Serializer\Exclude
     */
    private $transaction;

    /**
     * Transaction getter
     *
     * @return Transaction
     */
    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * Transaction setter
     *
     * @param Transaction $transaction
     */
    public function setTransaction(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }
}
