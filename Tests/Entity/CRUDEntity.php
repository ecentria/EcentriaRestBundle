<?php
/*
 * This file is part of the Ecentria software.
 *
 * (c) 2014, OpticsPlanet, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\CoreRestBundle\Tests\Entity;

use Ecentria\Libraries\CoreRestBundle\Entity\AbstractCRUDEntity;

/**
 * CRUDEntity
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class CRUDEntity extends AbstractCRUDEntity
{
    /**
     * @var mixed
     */
    protected $id;

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        $this->id = $id;
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
