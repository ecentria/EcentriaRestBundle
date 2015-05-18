<?php
/*
 * This file is part of the ecentria group, inc. software.
 *
 * (c) 2015, ecentria group, inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\CoreRestBundle\Model\Timestampable;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Timestampable trait
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
     * @param \DateTime $createdAt
     *
     * @see \Ecentria\Libraries\CoreRestBundle\Model\Embedded\TimestampableInterface::setCreatedAt
     *
     * @return TimestampableTrait
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Ecentria\Libraries\CoreRestBundle\Model\Embedded\TimestampableInterface::getCreatedAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     *
     * @param \DateTime $updatedAt
     *
     * @see \Ecentria\Libraries\CoreRestBundle\Model\Embedded\TimestampableInterface::setUpdatedAt
     *
     * @return TimestampableTrait
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Ecentria\Libraries\CoreRestBundle\Model\Embedded\TimestampableInterface::getUpdatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
