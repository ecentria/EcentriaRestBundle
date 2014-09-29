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
    protected function view($data = null, $statusCode = null, array $headers = array())
    {
        $request = $this->get('request_stack')->getMasterRequest();
        $transaction = $request->get('transaction');
        if ($transaction) {
            $violations = $request->get('violations');
            $transaction = $request->get('transaction');
            $transactionHandler = $this->get('ecentria.transaction.handler');
            $response = $transactionHandler->handle($transaction, $data, $violations);
            return parent::view($response, $transaction->getStatus());
        }
        return parent::view($data, $statusCode, $headers);
    }
}