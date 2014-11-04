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

use Ecentria\Libraries\CoreRestBundle\Model\CollectionResponse;
use Ecentria\Libraries\CoreRestBundle\Model\Embedded\EmbeddedInterface;
use Ecentria\Libraries\CoreRestBundle\Services\Transaction\TransactionResponseManager;
use FOS\RestBundle\View\View;
use JMS\Serializer\Exception\ValidationFailedException;
use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

/**
 * Exception listener
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class ExceptionListener
{
    const DATA_ALIAS = 'data_alias';

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
     * This listener is created to have an ability
     * to control response for every exception that can be thrown.
     *
     * Future:
     * Response for method not allowed exception should also contain transaction.
     * So it is the best place to control response object
     *
     * @param GetResponseForExceptionEvent $event
     * @param string $name
     * @param ContainerAwareEventDispatcher $eventDispatcher
     */
    public function onKernelException(
        GetResponseForExceptionEvent $event,
        $name,
        ContainerAwareEventDispatcher $eventDispatcher
    ) {
        $exception = $event->getException();
        if ($exception instanceof ValidationFailedException) {
            $event->setResponse(
                $this->getValidationFailedExceptionResponse($event, $eventDispatcher, $exception)
            );
        }
    }

    /**
     * Get correct response for validation failed exception
     *
     * @param GetResponseForExceptionEvent $event
     * @param ContainerAwareEventDispatcher $eventDispatcher
     * @param ValidationFailedException $exception
     *
     * @return Response
     */
    private function getValidationFailedExceptionResponse(
        GetResponseForExceptionEvent $event,
        ContainerAwareEventDispatcher $eventDispatcher,
        ValidationFailedException $exception
    ) {
        $event->stopPropagation();
        $request = $event->getRequest();

        $transaction = $request->get('transaction');

        if (!$transaction) {
            return $event->getResponse();
        }

        $data = $request->get(
            $request->get(self::DATA_ALIAS)
        );

        $violations = $exception->getConstraintViolationList();
        $request->attributes->set('violations', $violations);

        $view = View::create($data);
        $responseEvent = new GetResponseForControllerResultEvent(
            $event->getKernel(),
            $request,
            $request->getMethod(),
            $view
        );
        $eventDispatcher->dispatch('kernel.view', $responseEvent);
        $responseData = $view->getData();

        if ($responseData instanceof EmbeddedInterface) {
            $responseData->setShowAssociations(true);
        }

        if ($responseData instanceof CollectionResponse) {
            $responseData->setInheritedShowAssociations(false);
        }

        return $responseEvent->getResponse();
    }
}
