<?php
/*
 * This file is part of the Ecentria software.
 *
 * (c) 2015, Ecentria, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\CoreRestBundle\Model\Validatable;

use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Validatable interface
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
interface ValidatableInterface
{
    /**
     * Set violations
     *
     * @param ConstraintViolationListInterface $violations violations
     * @return mixed
     */
    public function setViolations(ConstraintViolationListInterface $violations);

    /**
     * Get violations
     *
     * @return ConstraintViolationListInterface
     */
    public function getViolations();

    /**
     * Set valid
     *
     * @param bool $valid valid
     * @return mixed
     */
    public function setValid($valid);

    /**
     * Is valid
     *
     * @return bool
     */
    public function isValid();
}
