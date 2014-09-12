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

use Symfony\Component\Validator\Constraints\Email as BaseEmail;

/**
 * Extension of email validator constraint
 *
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class Email extends BaseEmail
{
    /**
     * Checking MX
     *
     * @var bool
     */
    public $checkMX = true;

    /**
     * Checking host
     *
     * @var bool
     */
    public $checkHost = true;

    /**
     * Strict validation
     *
     * @var bool
     */
    public $strict = true;

    /**
     * Name of method to get email
     *
     * @var string
     */
    public $emailGetter;

    /**
     * Name of method to get dependency value
     * Like "getType()" must be equal to "email" to validate value
     *
     * @var string
     */
    public $dependencyGetter;

    /**
     * Value that must match to self::$dependencyGetter result to proceed validation
     *
     * @var string
     */
    public $dependencyMatch;

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'email';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
