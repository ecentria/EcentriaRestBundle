<?php
/*
 * This file is part of the ecentria group, inc. software.
 *
 * (c) 2015, ecentria group, inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\EcentriaRestBundle\Model\CRUD;

use Ecentria\Libraries\EcentriaRestBundle\Model\Embedded\EmbeddedInterface,
    Ecentria\Libraries\EcentriaRestBundle\Model\Timestampable\TimestampableInterface,
    Ecentria\Libraries\EcentriaRestBundle\Model\Transactional\TransactionalInterface;

/**
 * CRUD entity interface
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
interface CrudEntityInterface
{
    /**
     * Id getter
     *
     * @return mixed
     */
    public function getId();

    /**
     * Id setter
     *
     * @param mixed $id id
     *
     * @return CrudEntityInterface
     */
    public function setId($id);

    /**
     * Returns an array that is enough to update entity
     *
     * @return array
     */
    public function toArray();
}
