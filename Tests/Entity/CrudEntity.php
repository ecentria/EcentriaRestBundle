<?php
/*
 * This file is part of the ecentria group, inc. software.
 *
 * (c) 2015, ecentria group, inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\EcentriaRestBundle\Tests\Entity;

use Ecentria\Libraries\EcentriaRestBundle\Entity\AbstractCrudEntity;

/**
 * CrudEntity
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class CrudEntity extends AbstractCrudEntity
{
    /**
     * Identifiers
     *
     * @var mixed
     */
    protected $ids;

    /**
     * Primary Key
     *
     * @var mixed
     */
    protected $id;

    /**
     * {@inheritdoc}
     */
    public function getPrimaryKey()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getIds()
    {
        return $this->ids;
    }

    /**
     * {@inheritdoc}
     */
    public function setIds($ids)
    {
        $this->ids = $ids;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return array(
            'id' => $this->id
        );
    }
}
