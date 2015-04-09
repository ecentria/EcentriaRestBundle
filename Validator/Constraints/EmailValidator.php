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

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\EmailValidator as BaseEmailValidator;
use Symfony\Component\Validator\ConstraintViolation;

/**
 * Extension of email validator
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class EmailValidator extends BaseEmailValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($entity, Constraint $constraint)
    {
        if (!$constraint instanceof Email) {
            throw new \Exception('This constraint must be instance of EcentriaCommunicationApiBundle:Email');
        }
        $method = $constraint->dependencyGetter;
        $getter = $constraint->emailGetter;
        if (method_exists($entity, $method) && method_exists($entity, $getter)) {
            if ($entity->$method() === $constraint->dependencyMatch) {
                parent::validate($entity->$getter(), $constraint);
                $violations = $this->context->getViolations();
                foreach ($violations as $key => $violation) {
                    /** @var ConstraintViolation $violation */
                    $constraintViolation = new ConstraintViolation(
                        $violation->getMessage(),
                        $violation->getMessageTemplate(),
                        $violation->getParameters(),
                        $violation->getRoot(),
                        $constraint->propertyPath,
                        $violation->getInvalidValue(),
                        $violation->getPlural(),
                        $violation->getCode()
                    );
                    $violations->set($key, $constraintViolation);
                }

            }
        }
    }
}
