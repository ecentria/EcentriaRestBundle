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

use Ecentria\Libraries\EcentriaRestBundle\Model\CRUD\CrudEntityInterface,
    Ecentria\Libraries\EcentriaRestBundle\Model\Transactional\TransactionalInterface;

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
        $this->assertTrue($this->CrudEntity instanceof TransactionalInterface);

        // Transactional
        $this->assertTrue(method_exists($this->CrudEntity, 'setTransaction'));
        $this->assertTrue(method_exists($this->CrudEntity, 'getTransaction'));
    }
}
