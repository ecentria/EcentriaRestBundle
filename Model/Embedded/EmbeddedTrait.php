<?php
/*
 * This file is part of the ecentria group, inc. software.
 *
 * (c) 2015, ecentria group, inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\CoreRestBundle\Model\Embedded;

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
     * @param bool|null $showAssociations
     *
     * @see \Ecentria\Libraries\CoreRestBundle\Model\Embedded\EmbeddedInterface::setShowAssociations
     *
     * @return EmbeddedTrait
     */
    public function setShowAssociations($showAssociations)
    {
        $this->showAssociations = $showAssociations;
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Ecentria\Libraries\CoreRestBundle\Model\Embedded\EmbeddedInterface::showAssociations
     *
     * @return bool|null
     */
    public function showAssociations()
    {
        return $this->showAssociations;
    }
}
