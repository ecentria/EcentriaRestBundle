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

/**
 * Transactional interface
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
interface TimestampableInterface
{
    /**
     * Created at getter
     *
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * Created setter
     *
     * @param \DateTime $createdAt
     * @return TimestampableInterface
     */
    public function setCreatedAt(\DateTime $createdAt);

    /**
     * Updated at getter
     *
     * @return \DateTime
     */
    public function getUpdatedAt();

    /**
     * Set updated at
     *
     * @param \DateTime $updatedAt
     * @return TimestampableInterface
     */
    public function setUpdatedAt(\DateTime $updatedAt);
}
