<?php
/*
 * This file is part of the ecentria group, inc. software.
 *
 * (c) 2015, ecentria group, inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\EcentriaRestBundle\Tests\Services;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ParameterBag;
use Ecentria\Libraries\EcentriaRestBundle\Model\Transaction;
use Ecentria\Libraries\EcentriaRestBundle\Services\Transaction\TransactionUpdater;

/**
 * Transaction model test
 *
 * @author Son Dang <son.dang@opticsplanet.com>
 */
class TransactionUpdaterTest extends TestCase
{
    /**
     * Transaction Updater
     *
     * @var TransactionUpdater
     */
    protected $transactionUpdater;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->transactionUpdater = new TransactionUpdater();
    }

    public function testUpdateResponseTime()
    {
        $currentTimestamp = microtime(true);
        $startTimestamp = $currentTimestamp - 0.5;

        $transaction = new Transaction();
        $this->assertEquals(0, $transaction->getResponseTime());

        $request = new Request;
        $request->server = new ParameterBag(array('REQUEST_TIME_FLOAT' => $startTimestamp));

        $transaction = $this->transactionUpdater->updateResponseTime($transaction, $request, $currentTimestamp);
        $this->assertEquals(500, $transaction->getResponseTime());
    }
}