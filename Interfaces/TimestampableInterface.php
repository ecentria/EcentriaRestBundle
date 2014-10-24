<?php
/*
 * This file is part of the Ecentria software.
 *
 * (c) 2014, OpticsPlanet, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\CoreRestBundle\Interfaces;

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
     * @return self
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
     * @return self
     */
    public function setUpdatedAt(\DateTime $updatedAt);
}