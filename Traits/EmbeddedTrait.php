<?php
/*
 * This file is part of the Ecentria software.
 *
 * (c) 2014, OpticsPlanet, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\CoreRestBundle\Traits;

/**
 * Embedded trait
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
trait EmbeddedTrait
{
    /**
     * Associations?
     *
     * @var bool|null
     */
    protected $showAssociations = null;

    /**
     * {@inheritdoc}
     *
     * @see \Ecentria\Libraries\CoreRestBundle\Interfaces\EmbeddedInterface::setShowAssociations
     */
    public function setShowAssociations($showAssociations)
    {
        $this->showAssociations = $showAssociations;
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Ecentria\Libraries\CoreRestBundle\Interfaces\EmbeddedInterface::showAssociations
     */
    public function showAssociations()
    {
        return $this->showAssociations;
    }
}