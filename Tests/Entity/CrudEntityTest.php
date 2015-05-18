<?php
/*
 * This file is part of the ecentria group, inc. software.
 *
 * (c) 2015, ecentria group, inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\CoreRestBundle\Tests\Entity;

use Ecentria\Libraries\CoreRestBundle\Model\CRUD\CrudEntityInterface,
    Ecentria\Libraries\CoreRestBundle\Model\Embedded\EmbeddedInterface,
    Ecentria\Libraries\CoreRestBundle\Model\Timestampable\TimestampableInterface,
    Ecentria\Libraries\CoreRestBundle\Model\Transactional\TransactionalInterface;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

/**
 * CrudEntity
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class CrudEntityTest extends TestCase
{
    /**
     * CRUD Entity
     *
     * @var CrudEntity
     */
    protected $CrudEntity;

    /**
     * Setting up
     *
     * @return void
     */
    protected function setUp()
    {
        $this->CrudEntity = new CrudEntity();
    }

    /**
     * Testing that all methods exists
     *
     * @return void
     */
    public function testExtendedFunctionality()
    {
        $this->assertTrue($this->CrudEntity instanceof CrudEntityInterface);

        $this->assertTrue($this->CrudEntity instanceof EmbeddedInterface);
        $this->assertTrue($this->CrudEntity instanceof TransactionalInterface);
        $this->assertTrue($this->CrudEntity instanceof TimestampableInterface);

        // Transactional
        $this->assertTrue(method_exists($this->CrudEntity, 'setTransaction'));
        $this->assertTrue(method_exists($this->CrudEntity, 'getTransaction'));

        // Embedded
        $this->assertTrue(method_exists($this->CrudEntity, 'setShowAssociations'));
        $this->assertTrue(method_exists($this->CrudEntity, 'showAssociations'));

        // Timestampable
        $this->assertTrue(method_exists($this->CrudEntity, 'setCreatedAt'));
        $this->assertTrue(method_exists($this->CrudEntity, 'getCreatedAt'));
        $this->assertTrue(method_exists($this->CrudEntity, 'setUpdatedAt'));
        $this->assertTrue(method_exists($this->CrudEntity, 'getUpdatedAt'));
    }
}
