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

use Doctrine\Common\Annotations\Annotation;

/**
 * PropertyRestriction
 *
 * @Annotation
 * @Target("PROPERTY")
 *
 * Used for model (entity) property to avoid update or create.
 * As parameter it gets array of actions: {“update”, “create"}
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
final class PropertyRestriction extends Annotation
{
    const NAME = 'Ecentria\\Libraries\\EcentriaRestBundle\\Annotation\\PropertyRestriction';

    /**
     * Is granted for:
     * - create
     * - update
     * - all (default)
     *
     * @param string $action Action
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
