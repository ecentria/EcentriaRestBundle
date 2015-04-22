<?php
/*
 * This file is part of the Ecentria software.
 *
 * (c) 2014, OpticsPlanet, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\CoreRestBundle\Validator\Constraints;

use Ecentria\Libraries\CoreRestBundle\Entity\Transaction;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Transaction success validator
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class TransactionSuccessValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($entity, Constraint $constraint)
    {
        if (!$constraint instanceof TransactionSuccess) {
            throw new \Exception(
                'This constraint must be instance of EcentriaCommunicationApiBundle:TransactionSuccess'
            );
        }

        if (!$entity instanceof Transaction) {
            throw new \Exception('This entity must be instance of EcentriaCommunicationApiBundle:Transaction');
        }

        $success = $entity->getSuccess();
        $status = $entity->getStatus();

        if ($status < 400) {
            $correctSuccess = true;
        } else {
            $correctSuccess = false;
        }

        if ($success != $correctSuccess) {
            $this->context->addViolation(
                sprintf(
                    'Transaction status is not correct. Should be %s',
                    $correctSuccess
                )
            );
        }
    }
}
