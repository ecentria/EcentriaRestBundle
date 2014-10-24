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

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Transactional trait
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
trait TimestampableTrait
{
    /**
     * Created at given datetime
     *
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    protected $createdAt;

    /**
     * Updated at given datetime
     *
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    protected $updatedAt;

    /**
     * {@inheritdoc}
     *
     * @see \Ecentria\Libraries\CoreRestBundle\Interfaces\TimestampableInterface::setCreatedAt
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Ecentria\Libraries\CoreRestBundle\Interfaces\TimestampableInterface::getCreatedAt
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Ecentria\Libraries\CoreRestBundle\Interfaces\TimestampableInterface::setUpdatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Ecentria\Libraries\CoreRestBundle\Interfaces\TimestampableInterface::getUpdatedAt
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}