<?php
/*
 * This file is part of the Ecentria software.
 *
 * (c) 2014, OpticsPlanet, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\CoreRestBundle\EventListener;

use Ecentria\Libraries\CoreRestBundle\Entity\Transaction;
use Ecentria\Libraries\CoreRestBundle\Model\CollectionResponse;
use Ecentria\Libraries\CoreRestBundle\Model\Embedded\EmbeddedInterface;
use Ecentria\Libraries\CoreRestBundle\Services\Transaction\TransactionResponseManager;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

/**
 * Embedded response listener
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class TransactionResponseListener
{
    /**
     * @var TransactionResponseManager
     */
    private $transactionResponseManager;

    /**
     * Constructor
     *
     * @param TransactionResponseManager $transactionResponseManager
     */
    public function __construct(TransactionResponseManager $transactionResponseManager)
    {
        $this->transactionResponseManager = $transactionResponseManager;
    }

    /**
     * Let's process transaction
     *
     * @param GetResponseForControllerResultEvent $event
     * @return void
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $view = $event->getControllerResult();

        if (!$view instanceof View) {
            return;
        }

        $request = $event->getRequest();
        $transaction = $request->get('transaction');

        if ($transaction instanceof Transaction) {
            $data = $view->getData();
            $violations = $request->get('violations');
            $view->setData(
                $this->transactionResponseManager->handle($transaction, $data, $violations)
            );
            $view->setStatusCode($transaction->getStatus());
        }

    }
}