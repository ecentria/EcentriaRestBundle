<?php
/*
 * This file is part of the Ecentria software.
 *
 * (c) 2014, OpticsPlanet, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\CoreRestBundle\Services;

use Doctrine\Common\Collections\ArrayCollection;
use Ecentria\Libraries\CoreRestBundle\Model\Error;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class ErrorBuilder
{
    /**
     * Errors
     *
     * @var ArrayCollection
     */
    private $errors;

    /**
     * Violations
     *
     * @var ConstraintViolation[]|ArrayCollection
     */
    private $violations;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->errors = new ArrayCollection();
        $this->violations = new ArrayCollection();
    }

    /**
     * Process violations
     *
     * @param ConstraintViolationList|ConstraintViolation[] $violations
     */
    public function processViolations(ConstraintViolationList $violations = null)
    {
        if (is_null($violations)) {
            return;
        }
        $this->violations = $violations;
        foreach ($violations as $violation) {
            $this->processViolation($violation);
        }
    }

    /**
     * Process violation
     *
     * @param ConstraintViolation $violation
     */
    private function processViolation(ConstraintViolation $violation)
    {
        $id = $violation->getRoot()->getId();
        $errors = $this->getEntityErrors($id);
        $context = $this->determineContext($violation);
        $error = new Error(
            $violation->getMessage(),
            $violation->getCode(),
            $violation->getPropertyPath(),
            $context
        );
        $errors->add($error);
        $this->errors->set($id, $errors);
    }

    /**
     *
     * @param ConstraintViolation $violation
     * @return string
     */
    private function determineContext(ConstraintViolation $violation)
    {
        $parameters = $violation->getParameters();
        $isGlobal = isset($parameters['context']) && $parameters['context'] === Error::CONTEXT_GLOBAL;
        return $isGlobal ? Error::CONTEXT_GLOBAL : Error::CONTEXT_DATA;
    }


    /**
     * Errors getter
     *
     * @param null|string $context
     * @return ArrayCollection
     */
    public function getErrors($context = null)
    {
        $errors = new ArrayCollection();
        foreach ($this->errors as $errorContainer) {
            foreach ($errorContainer as $errorItem) {
                if ($errorItem instanceof Error) {
                    if (!is_null($context)) {
                        if ($errorItem->context == Error::CONTEXT_GLOBAL) {
                            $errors->add($errorItem);
                        }
                    } else {
                        $errors->add($errorItem);
                    }
                }
            }
        }
        return $errors;
    }

    /**
     * Has errors
     *
     * @return bool
     */
    public function hasErrors()
    {
        return (bool) $this->errors->count();
    }

    /**
     * Entity errors getter
     *
     * @param $key
     * @return ArrayCollection|mixed|null
     */
    public function getEntityErrors($key)
    {
        $errors = $this->errors->get($key);
        if (is_null($errors)) {
            $errors = new ArrayCollection();
        }
        return $errors;
    }

    /**
     * Custom error adder
     *
     * @param $id
     * @param Error $error
     */
    public function addCustomError($id, Error $error)
    {
        $errors = $this->getEntityErrors($id);
        $errors->add($error);
        $this->errors->set($id, $errors);
    }


}