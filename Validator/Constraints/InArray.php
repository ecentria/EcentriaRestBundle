<?php
/*
 * This file is part of the OpCart software.
 *
 * (c) 2014, OpticsPlanet, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Ecentria\Libraries\CoreRestBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * In array constraint
 *
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class InArray extends Constraint
{
    /**
     * @var array
     */
    public $values = array();

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'in_array';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
