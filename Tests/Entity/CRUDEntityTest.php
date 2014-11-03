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
     * @var CRUDEntityTest
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

        $this->assertTrue(method_exists($this->CRUDEntity, 'setTransaction'));
        $this->assertTrue(method_exists($this->CRUDEntity, 'getTransaction'));
        $this->assertTrue(method_exists($this->CRUDEntity, 'setShowAssociations'));
        $this->assertTrue(method_exists($this->CRUDEntity, 'showAssociations'));
        $this->assertTrue(method_exists($this->CRUDEntity, 'setCreatedAt'));
        $this->assertTrue(method_exists($this->CRUDEntity, 'getCreatedAt'));
        $this->assertTrue(method_exists($this->CRUDEntity, 'setUpdatedAt'));
        $this->assertTrue(method_exists($this->CRUDEntity, 'getUpdatedAt'));
    }
}
