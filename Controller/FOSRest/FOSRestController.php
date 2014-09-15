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

use Ecentria\Libraries\CoreRestBundle\Entity\CRUDEntity;
use Ecentria\Libraries\CoreRestBundle\Entity\NullEntity;
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
    protected function viewTransaction($transaction, $entity, ConstraintViolationList $violations = null)
    {
        $transactionHandler = $this->get('ecentria.transaction.handler');
        $transaction = $transactionHandler->handle($transaction, $entity, $violations);
        $em = $this->get('doctrine.orm.default_entity_manager');

        $transaction->setRequestId('1');

        $em->persist($transaction);
        $em->flush($transaction);

        if ($entity instanceof CRUDEntity) {
            $entity->setTransaction($transaction);
        }

        if ($transaction->getSuccess()) {
            $data = $entity;
        } else {
            $data = new NullEntity();
            $data->setTransaction($transaction);
        }

        return parent::view($data, $transaction->getStatus());
    }
}