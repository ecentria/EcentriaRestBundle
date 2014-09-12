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

use Doctrine\Common\Annotations\Annotation;

/**
 *
 * @Annotation
 * @Target("PROPERTY")
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
final class PropertyRestriction extends Annotation
{
    const NAME = 'Ecentria\Libraries\CoreRestBundle\Annotation\PropertyRestriction';

    /**
     * Is granted for:
     * - create
     * - update
     * - all (default)
     *
     * @param string $action
     * @return bool
     */
    public function isGranted($action)
    {
        if ($this->value === null || $this->value === 'all') {
            return false;
        }
        if (!is_string($action)) {
            return false;
        }
        if (is_array($this->value)) {
            return !in_array($action, $this->value);
        } else {
            return $this->value !== $action;
        }
    }
}