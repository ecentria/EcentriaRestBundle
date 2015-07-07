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
abstract class AbstractCrudEntity implements CrudEntityInterface
{
    use EmbeddedTrait;
    use TransactionalTrait;

    /**
     * Primary key getter
     *
     * @return mixed
     */
    abstract public function getPrimaryKey();

    /**
     * Ids getter
     * array of 'id' => value - Used to generate routes: i.e /author/{authorId}/book/{bookId}
     *
     * @return array
     */
    abstract public function getIds();

    /**
     * Ids setter
     *
     * @param array $ids array of 'id' => value - should set the property of each 'id' with the value
     *
     * @return CrudEntityInterface
     */
    abstract public function setIds($ids);

    /**
     * Returns an array that is enough to update entity
     *
     * @return array
     */
    abstract public function toArray();
}
