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

use Ecentria\Libraries\CoreRestBundle\Interfaces\CRUDEntityInterface,
    Ecentria\Libraries\CoreRestBundle\Traits\EmbeddedTrait,
    Ecentria\Libraries\CoreRestBundle\Traits\TransactionalTrait,
    Ecentria\Libraries\CoreRestBundle\Traits\TimestampableTrait;

/**
 * Abstract CRUD entity class
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
abstract class CRUDEntity implements CRUDEntityInterface
{
    use EmbeddedTrait;
    use TransactionalTrait;
    use TimestampableTrait;

    /**
     * {@inheritdoc}
     */
    abstract public function getId();

    /**
     * {@inheritdoc}
     */
    abstract public function setId($id);
}
