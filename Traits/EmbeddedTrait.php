<?php

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
     */
    public function setShowAssociations($showAssociations)
    {
        $this->showAssociations = $showAssociations;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function showAssociations()
    {
        return $this->showAssociations;
    }
}