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
     * Test transaction response time is calculated when kernel terminates
     */
    public function testKernelTerminateCalculatesResponseTime()
    {
        $transaction = new Transaction();
        $this->listener->onKernelTerminate($this->preparePostResponseEvent($transaction));
        $this->assertGreaterThan(0, $transaction->getResponseTime());
    }

    /**
     * Prepare PostResponseEvent
     *
     * @param Transaction $transaction
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function preparePostResponseEvent(Transaction $transaction)
    {
        $attributes = $this->getMockBuilder('\Symfony\Component\HttpFoundation\ParameterBag')
            ->getMock();
        $attributes->expects($this->once())
            ->method('get')
            ->willReturn($transaction);

        $request = $this->getMockBuilder('\Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock();
        $request->attributes = $attributes;
        $request->server = new ParameterBag(array('REQUEST_TIME_FLOAT' => microtime(true) - 0.5));

        $postResponseEvent = $this->getMockBuilder('\Symfony\Component\HttpKernel\Event\PostResponseEvent')
            ->disableOriginalConstructor()
            ->getMock();
        $postResponseEvent->expects($this->once())
            ->method('getRequest')
            ->willReturn($request);

        return $postResponseEvent;
    }

}
