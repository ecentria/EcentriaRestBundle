<?php
/*
 * This file is part of the Ecentria software.
 *
 * (c) 2014, OpticsPlanet, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\CoreRestBundle\Annotation;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationAnnotation;

/**
 * @Annotation
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class AvoidTransaction extends ConfigurationAnnotation
{
    const NAME = 'Ecentria\\Libraries\\CoreRestBundle\\Annotation\\AvoidTransaction';

    /**
     * {@inheritDoc}
     */
    public function getAliasName()
    {
        return 'avoid_transaction';
    }

    /**
     * {@inheritDoc}
     */
    public function allowArray()
    {
        return false;
    }
}