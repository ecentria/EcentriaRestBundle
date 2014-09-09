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
 * Circular reference constraint
 *
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class CircularReference extends Constraint
{
    /**
     * @var string field name
     */
    public $field = '';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'circular_reference';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
