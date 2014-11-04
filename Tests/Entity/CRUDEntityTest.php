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

use Ecentria\Libraries\CoreRestBundle\Model\CRUD\CRUDEntityInterface,
    Ecentria\Libraries\CoreRestBundle\Model\Embedded\EmbeddedInterface,
    Ecentria\Libraries\CoreRestBundle\Model\Timestampable\TimestampableInterface,
    Ecentria\Libraries\CoreRestBundle\Model\Transactional\TransactionalInterface;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

/**
 * CRUDEntity
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class CRUDEntityTest extends TestCase
{
    /**
     * CRUD Entity
     *
     * @var CRUDEntity
     */
    protected $CRUDEntity;

    /**
     * Setting up
     */
    protected function setUp()
    {
        $this->CRUDEntity = new CRUDEntity();
    }

    /**
     * Testing that all methods exists
     */
    public function testExtendedFunctionality()
    {
        $this->assertTrue($this->CRUDEntity instanceof CRUDEntityInterface);

        $this->assertTrue($this->CRUDEntity instanceof EmbeddedInterface);
        $this->assertTrue($this->CRUDEntity instanceof TransactionalInterface);
        $this->assertTrue($this->CRUDEntity instanceof TimestampableInterface);

        // Transactional
        $this->assertTrue(method_exists($this->CRUDEntity, 'setTransaction'));
        $this->assertTrue(method_exists($this->CRUDEntity, 'getTransaction'));

        // Embedded
        $this->assertTrue(method_exists($this->CRUDEntity, 'setShowAssociations'));
        $this->assertTrue(method_exists($this->CRUDEntity, 'showAssociations'));

        // Timestampable
        $this->assertTrue(method_exists($this->CRUDEntity, 'setCreatedAt'));
        $this->assertTrue(method_exists($this->CRUDEntity, 'getCreatedAt'));
        $this->assertTrue(method_exists($this->CRUDEntity, 'setUpdatedAt'));
        $this->assertTrue(method_exists($this->CRUDEntity, 'getUpdatedAt'));
    }
}
