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
interface CrudEntityInterface extends EmbeddedInterface, TransactionalInterface
{
    /**
     * Primary key getter
     *
     * @return mixed
     */
    public function getPrimaryKey();

    /**
     * Ids getter
     * array of 'id' => value - Used to generate routes: i.e /author/{authorId}/book/{bookId}
     *
     * @return array
     */
    public function getIds();

    /**
     * Ids setter
     *
     * @param array $ids array of 'id' => value - should set the property of each 'id' with the value
     *
     * @return CrudEntityInterface
     */
    public function setIds($ids);

    /**
     * Returns an array that is enough to update entity
     *
     * @return array
     */
    public function toArray();
}
