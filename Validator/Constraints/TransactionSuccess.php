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

/**
 * In array constraint
 *
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class TransactionSuccess extends Constraint
{
    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'transaction_success';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
