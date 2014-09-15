<?php
/*
 * This file is part of the Ecentria software.
 *
 * (c) 2014, OpticsPlanet, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\CoreRestBundle\Controller\FOSRest;

use Ecentria\Libraries\CoreRestBundle\Entity\Transaction;
use FOS\RestBundle\Controller\FOSRestController as BaseFOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * FOS Rest Controller
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 *
 */
class FOSRestController extends BaseFOSRestController implements ClassResourceInterface
{
    /**
     * {@inheritdoc}
     */
    protected function viewTransaction($unitOfWork, ConstraintViolationList $violations = null)
    {
        $request = $this->get('request_stack')->getMasterRequest();
        $transaction = $request->get('transaction');

        if ($transaction instanceof Transaction) {
            $transactionHandler = $this->get('ecentria.transaction.handler');
            $data = $transactionHandler->handle($transaction, $unitOfWork, $violations);
        } else {
            $data = $unitOfWork;
        }

        return parent::view($data, $transaction->getStatus());
    }
}