<?php
/*
 * This file is part of the ecentria group, inc. software.
 *
 * (c) 2015, ecentria group, inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\EcentriaRestBundle\Tests\Model;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Ecentria\Libraries\EcentriaRestBundle\Model\Transaction;

/**
 * Transaction model test
 *
 * @author Son Dang <son.dang@opticsplanet.com>
 */
class TransactionTest extends TestCase
{
    public function testCalculateResponseTime()
    {
        $transaction = new Transaction();
        $transaction->calculateResponseTime(microtime(true));
        $responseTimeCheck = round((microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']) * 1000);

        $this->assertGreaterThan(0, $transaction->getResponseTime());
        $this->assertTrue(abs($responseTimeCheck - $transaction->getResponseTime()) < 10); // Allow some leeway
    }
}