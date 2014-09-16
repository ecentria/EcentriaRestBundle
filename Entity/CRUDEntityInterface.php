<?php
/*
 * This file is part of the Ecentria software.
 *
 * (c) 2014, OpticsPlanet, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\CoreRestBundle\Entity;

/**
 * CRUD entity interface
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
interface CRUDEntityInterface
{
    /**
     * CreatedAt setter
     *
     * @param \DateTime $createdAt
     *
     * @return self
     */
    public function setCreatedAt(\DateTime $createdAt);

    /**
     * CreatedAt getter
     *
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * UpdatedAt setter
     *
     * @param \DateTime $updatedAt
     *
     * @return self
     */
    public function setUpdatedAt(\DateTime $updatedAt);

    /**
     * UpdatedAt getter
     *
     * @return \DateTime
     */
    public function getUpdatedAt();

    /**
     * Id getter
     *
     * @return mixed
     */
    public function getId();

    /**
     * Id setter
     *
     * @param mixed $id
     *
     * @return self
     */
    public function setId($id);
}
