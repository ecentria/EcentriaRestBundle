<?php
/*
 * This file is part of the ecentria group, inc. software.
 *
 * (c) 2015, ecentria group, inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\EcentriaRestBundle\Services;

use Doctrine\Common\Collections\ArrayCollection;
use Ecentria\Libraries\EcentriaRestBundle\Model\Transaction;
use Ecentria\Libraries\EcentriaRestBundle\Model\CRUD\CrudEntityInterface;
use Ecentria\Libraries\EcentriaRestBundle\Model\Error;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * Error Builder
 *
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
     * Constructor
     */
    public function __construct()
    {
        $this->errors = new ArrayCollection();
    }

    /**
     * Process violations
     *
     * @param ConstraintViolationList|ConstraintViolation[] $violations
     *
     * @return void
     */
    public function processViolations(ConstraintViolationList $violations = null)
    {
        if (is_null($violations)) {
            return;
        }
        foreach ($violations as $violation) {
            $this->processViolation($violation);
        }
    }

    /**
     * Process violation
     *
     * @param ConstraintViolation $violation
     *
     * @return void
     */
    private function processViolation(ConstraintViolation $violation)
    {
        $id = $violation->getRoot() instanceof CrudEntityInterface ? $violation->getRoot()->getPrimaryKey() : null;
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
     * Determine context
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
                        if ($errorItem->context == $context) {
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
     * @param string $key
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
     * @param string|int $id
     * @param Error $error
     *
     * @return void
     */
    public function addCustomError($id, Error $error)
    {
        $errors = $this->getEntityErrors($id);
        $errors->add($error);
        $this->errors->set($id, $errors);
    }

    /**
     * Setting transaction errors
     *
     * @param Transaction &$transaction
     *
     * @return void
     */
    public function setTransactionErrors(Transaction &$transaction)
    {
        $messages = new ArrayCollection();
        $globalErrors = $this->getErrors(Error::CONTEXT_GLOBAL);

        if (!$globalErrors->isEmpty()) {
            $transaction->setStatus(Transaction::STATUS_NOT_FOUND);
        }

        $errors = $this->getErrors();
        if (!$errors->isEmpty()) {
            $messages->set('errors', $errors);
        }

        if ($messages->count()) {
            $transaction->setMessages($messages);
        }
    }
}
