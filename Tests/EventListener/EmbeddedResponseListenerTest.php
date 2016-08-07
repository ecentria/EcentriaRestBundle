<?php
/*
 * This file is part of the ecentria group, inc. software.
 *
 * (c) 2015, ecentria group, inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\EcentriaRestBundle\Tests\EventListener;

use Ecentria\Libraries\EcentriaRestBundle\Services\Embedded\EmbeddedManager;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Ecentria\Libraries\EcentriaRestBundle\EventListener\EmbeddedResponseListener;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

/**
 * Embedded response listener test
 *
 * @property \PHPUnit_Framework_MockObject_MockObject|EmbeddedManager                     manager
 * @property \PHPUnit_Framework_MockObject_MockObject|GetResponseForControllerResultEvent event
 * @property EmbeddedResponseListener                                                     listener
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class EmbeddedResponseListenerTest extends TestCase
{
    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->manager = $this->getMock('Ecentria\Libraries\EcentriaRestBundle\Services\Embedded\EmbeddedManager');
        $this->event = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent')
            ->disableOriginalConstructor()
            ->getMock();
        $this->listener = new EmbeddedResponseListener($this->manager);
    }

    /**
     * Test incorrect controller result passed
     *
     * @return void
     */
    public function testIncorrectControllerResultPassed()
    {
        $this->event->expects($this->once())
            ->method('getControllerResult')
            ->willReturn(new \stdClass());

        $this->manager->expects($this->never())
            ->method('generateGroups');
        $this->listener->onKernelView($this->event);
    }

    /**
     * Test incorrect controller result passed
     *
     * @return void
     */
    public function testSerializationGroupsArePassedToContext()
    {
        $request = $this->getMock('Symfony\Component\HttpFoundation\Request');
        $view = new View();
        $groups = ['group1', 'group2'];
        $this->event->expects($this->once())
            ->method('getControllerResult')
            ->willReturn($view);
        $this->event->expects($this->once())
            ->method('getRequest')
            ->willReturn($request);
        $this->manager->expects($this->once())
            ->method('generateGroups')
            ->willReturn($groups);
        $this->listener->onKernelView($this->event);
        $this->assertSame([$groups], $view->getContext()->getAttributes());
    }
}
