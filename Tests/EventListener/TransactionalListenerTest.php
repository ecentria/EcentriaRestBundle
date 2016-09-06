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

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Ecentria\Libraries\EcentriaRestBundle\Model\Transaction;
use Ecentria\Libraries\EcentriaRestBundle\Services\Transaction\TransactionUpdater;
use Ecentria\Libraries\EcentriaRestBundle\EventListener\TransactionalListener;
use FOS\RestBundle\View\View;

/**
 * Transactional listener test
 *
 * @author Son Dang <son.dang@opticsplanet.com>
 */
class TransactionalListenerTest extends TestCase
{
    /**
     * Reader
     *
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $reader;

    /**
     * Transaction builder
     *
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $transactionBuilder;

    /**
     * Transaction storage
     *
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $transactionStorage;

    /**
     * Transaction response manager
     *
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $transactionResponseManager;

    /**
     * Transaction updater
     *
     * @var TransactionaUpdater
     */
    protected $transactionUpdater;

    /**
     * Transactional listener
     *
     * @var TransactionalListener
     */
    protected $listener;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->reader = $this->getMock('Doctrine\Common\Annotations\Reader');

        $erbNamespace = 'Ecentria\Libraries\EcentriaRestBundle';
        $this->transactionBuilder = $this->getMock($erbNamespace . '\Services\Transaction\TransactionBuilder');
        $this->transactionStorage = $this->getMockBuilder($erbNamespace . '\Services\Transaction\Storage\Doctrine')
            ->disableOriginalConstructor()
            ->getMock();
        $this->transactionResponseManager = $this->getMockBuilder(
            $erbNamespace . '\Services\Transaction\TransactionResponseManager'
        )
        ->disableOriginalConstructor()
        ->getMock();
        $this->transactionUpdater = new TransactionUpdater();

        $this->listener = new TransactionalListener(
            $this->reader,
            $this->transactionBuilder,
            $this->transactionStorage,
            $this->transactionResponseManager,
            $this->transactionUpdater
        );
    }

    /**
     * Test transaction response time is calculated when kernel response event is fired
     */
    public function testKernelViewCalculatesResponseTime()
    {
        $transaction = new Transaction();
        $this->listener->onKernelView($this->prepareGetResponseForControllerResultEvent($transaction));
        $this->assertGreaterThan(0, $transaction->getResponseTime());
    }

    /**
     * Prepare GetResponseForControllerResultEvent
     *
     * @param Transaction $transaction
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function prepareGetResponseForControllerResultEvent(Transaction $transaction)
    {
        $request = $this->getMockBuilder('\Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects($this->at(0))
            ->method('get')
            ->with('transaction')
            ->willReturn($transaction);
        $request->expects($this->at(1))
            ->method('get')
            ->with('violations')
            ->willReturn(null);
        $request->server = new ParameterBag(array('REQUEST_TIME_FLOAT' => microtime(true) - 0.5));
        $request->attributes = new ParameterBag(array('methodtimes' => array()));

        $event = $this->getMockBuilder('\Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent')
            ->disableOriginalConstructor()
            ->getMock();
        $event->expects($this->once())
            ->method('getRequest')
            ->willReturn($request);
        $event->expects($this->once())
            ->method('getControllerResult')
            ->willReturn(new View());

        return $event;
    }

}
