<?php
/*
 * This file is part of the ecentria group, inc. software.
 *
 * (c) 2015, ecentria group, inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\EcentriaRestBundle\Entity;

use Ecentria\Libraries\EcentriaRestBundle\Model\CRUD\CrudEntityInterface,
    Ecentria\Libraries\EcentriaRestBundle\Model\Embedded\EmbeddedInterface,
    Ecentria\Libraries\EcentriaRestBundle\Model\Timestampable\TimestampableInterface,
    Ecentria\Libraries\EcentriaRestBundle\Model\Transactional\TransactionalInterface,
    Ecentria\Libraries\EcentriaRestBundle\Model\Transactional\TransactionalTrait,
    Ecentria\Libraries\EcentriaRestBundle\Model\Timestampable\TimestampableTrait,
    Ecentria\Libraries\EcentriaRestBundle\Model\Embedded\EmbeddedTrait;

/**
 * Abstract CRUD entity class
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
abstract class AbstractCrudEntity implements CrudEntityInterface, EmbeddedInterface, TransactionalInterface, TimestampableInterface
{
    use EmbeddedTrait;
    use TransactionalTrait;
    use TimestampableTrait;

    /**
     * Id getter
     *
     * @return mixed
     */
    abstract public function getId();

    /**
     * Id setter
     *
     * @param mixed $id id
     *
     * @return CrudEntityInterface
     */
    abstract public function setId($id);

    /**
     * Returns an array that is enough to update entity
     *
     * @return array
     */
    abstract public function toArray();
}
