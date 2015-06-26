<?php
/*
 * This file is part of the ecentria group, inc. software.
 *
 * (c) 2015, ecentria group, inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\EcentriaRestBundle\Model\Embedded;

/**
 * Embedded interface
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
interface EmbeddedInterface
{
    /**
     * ShowAssociations setter
     *
     * @param bool|null $showAssociations
     *
     * @return $this
     */
    public function setShowAssociations($showAssociations);

    /**
     * ShowAssociations getter
     *
     * @return bool|null
     */
    public function showAssociations();
}
