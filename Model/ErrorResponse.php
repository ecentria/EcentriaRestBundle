<?php
/*
 * This file is part of the Ecentria software.
 *
 * (c) 2015, Ecentria, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\CoreRestBundle\Model;

use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Error response
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class ErrorResponse
{
    /**
     * Errors
     *
     * @var array
     */
    private $errors = [];

    /**
     * Constructor
     *
     * @param ConstraintViolationListInterface $violations violations
     */
    public function __construct(ConstraintViolationListInterface $violations)
    {
        /** @var ConstraintViolation $violation */
        foreach ($violations as $violation) {
            $this->errors[$violation->getPropertyPath()] = [$violation->getMessage()];
        }
    }
}
