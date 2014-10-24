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

use Doctrine\ORM\Mapping as ORM;

use Ecentria\Libraries\CoreRestBundle\Interfaces\EmbeddedInterface,
    Ecentria\Libraries\CoreRestBundle\Interfaces\TransactionalInterface,
    Ecentria\Libraries\CoreRestBundle\Traits\EmbeddedTrait,
    Ecentria\Libraries\CoreRestBundle\Traits\TransactionalTrait,
    Ecentria\Libraries\CoreRestBundle\Traits\TimestampableTrait,
    Ecentria\Libraries\CoreRestBundle\Interfaces\TimestampableInterface;

use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Abstract CRUD entity class
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
abstract class CRUDEntity implements CRUDEntityInterface, EmbeddedInterface, TransactionalInterface, TimestampableInterface
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
