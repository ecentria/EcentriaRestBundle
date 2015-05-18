<?php
/*
 * This file is part of the ecentria group, inc. software.
 *
 * (c) 2015, ecentria
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\CoreRestBundle\Model\Validatable;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use JMS\Serializer\Annotation as JMS;

/**
 * Validatable trait
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
trait ValidatableTrait
{
    /**
     * Constraint violation list
     *
     * @var ConstraintViolationListInterface
     *
     * @JMS\ReadOnly()
     */
    private $violations;

    /**
     * Valid
     *
     * @var bool
     *
     * @JMS\ReadOnly()
     */
    private $valid;

    /**
     * Set violations
     *
     * @param ConstraintViolationListInterface $violations violations
     * @return mixed
     */
    public function setViolations(ConstraintViolationListInterface $violations)
    {
        $this->violations = $violations;
        return $this;
    }

    /**
     * Get violations
     *
     * @return ConstraintViolationListInterface
     */
    public function getViolations()
    {
        return $this->violations;
    }

    /**
     * Set valid
     *
     * @param bool $valid valid
     * @return mixed
     */
    public function setValid($valid)
    {
        $this->valid = filter_var($valid, FILTER_VALIDATE_BOOLEAN);
        return $this;
    }

    /**
     * Is valid
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->valid;
    }
}
