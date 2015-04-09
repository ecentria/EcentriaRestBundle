<?php
/*
 * This file is part of the Ecentria software.
 *
 * (c) 2014, OpticsPlanet, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\CoreRestBundle\Services\Transaction\Handler;

use Doctrine\Common\Collections\ArrayCollection;

use Ecentria\Libraries\CoreRestBundle\Entity\Transaction,
    Ecentria\Libraries\CoreRestBundle\Model\CollectionResponse,
    Ecentria\Libraries\CoreRestBundle\Model\CRUD\CrudEntityInterface;

use Symfony\Component\Validator\ConstraintViolationList;

/**
 * Transaction handler interface
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
interface TransactionHandlerInterface
{
    /**
     * Supports method
     *
     * @return string
     */
    public function supports();

    /**
     * Handle
     *
     * @param Transaction                         $transaction Transaction
     * @param CrudEntityInterface|ArrayCollection $data        Data
     * @param ConstraintViolationList|null        $violations  Violations
     *
     * @return CrudEntityInterface|CollectionResponse
     */
    public function handle(Transaction $transaction, $data, ConstraintViolationList $violations = null);
}
