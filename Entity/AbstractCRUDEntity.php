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

use Ecentria\Libraries\CoreRestBundle\Model\CRUD\CrudEntityInterface,
    Ecentria\Libraries\CoreRestBundle\Model\Transactional\TransactionalTrait,
    Ecentria\Libraries\CoreRestBundle\Model\Timestampable\TimestampableTrait,
    Ecentria\Libraries\CoreRestBundle\Model\Embedded\EmbeddedTrait;

/**
 * Abstract CRUD entity class
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
abstract class AbstractCrudEntity implements CrudEntityInterface
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
