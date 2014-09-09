<?php
/*
 * This file is part of the OpCart software.
 *
 * (c) 2014, OpticsPlanet, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\CoreRestBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Error Model
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class Error
{
    const CODE_CONFLICT = 409;
    const CODE_BAD_REQUEST = 400;
    const CODE_NOT_FOUND = 404;

    /**
     * Code
     *
     * @var int
     */
    protected $code;

    /**
     * Message
     *
     * @var string
     */
    protected $message;

    /**
     * Errors collection
     *
     * @var ArrayCollection
     */
    protected $errors;

    /**
     * Constructor
     *
     * @param $message
     * @param $code
     */
    public function __construct($message, $code)
    {
        $this->message = $message;
        $this->code = $code;
        $this->errors = new ArrayCollection();
    }

    /**
     * Code setter
     *
     * @param int $code
     *
     * @return self
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Code getter
     *
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Message setter
     *
     * @param string $message
     *
     * @return self
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Message getter
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Errors setter
     *
     * @param ArrayCollection $errors
     *
     * @return self
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;
        return $this;
    }

    /**
     * Errors getter
     *
     * @return ArrayCollection
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
