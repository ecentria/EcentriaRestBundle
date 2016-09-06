<?php
/*
 * This file is part of the ecentria group, inc. software.
 *
 * (c) 2015, ecentria group, inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\EcentriaRestBundle\Services\Transaction;

use Ecentria\Libraries\EcentriaRestBundle\Model\Transaction;
use Symfony\Component\HttpFoundation\Request;

/**
 * Transaction updater
 *
 * @author Son Dang <son.dang@opticsplanet.com>
 */
class TransactionUpdater
{
    /**
     * Calculate response time in milliseconds based on current and start of request timestamps
     *
     * @param Transaction $transaction
     * @param Request $request
     * @param float $currentTimestamp
     *
     * @return int
     */
    public function updateResponseTime(Transaction $transaction, Request $request, $currentTimestamp)
    {
        $requestStartTimestamp = $request->server->get('REQUEST_TIME_FLOAT');
        $responseTime = round(($currentTimestamp - $requestStartTimestamp) * 1000);
        $transaction->setResponseTime($responseTime);
        return $transaction;
    }

    /**
     * Add method times
     *
     * @param Transaction $transaction
     * @param Request $request
     *
     * @return Transaction
     */
    public function addMethodTimes(Transaction $transaction, Request $request)
    {
        $methodTimes = $request->attributes->get('methodTimes');
        if ($methodTimes) {
            $transaction->setMethodTimes($methodTimes);
        }
        return $transaction;
    }
}
