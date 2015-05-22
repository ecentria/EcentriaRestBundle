<?php
/*
 * This file is part of the ecentria group, inc. software.
 *
 * (c) 2015, ecentria group, inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\EcentriaRestBundle\Annotation;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationAnnotation;

/**
 * AvoidTransaction
 *
 * @Annotation
 *
 * Used for controller action to avoid creating transaction.
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class AvoidTransaction extends ConfigurationAnnotation
{
    const NAME = 'Ecentria\\Libraries\\EcentriaRestBundle\\Annotation\\AvoidTransaction';

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
