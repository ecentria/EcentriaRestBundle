<?php
/*
 * This file is part of the ecentria group, inc. software.
 *
 * (c) 2015, ecentria group, inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\EcentriaRestBundle\EventListener;

use Ecentria\Libraries\EcentriaRestBundle\Model\Alias;
use Ecentria\Libraries\EcentriaRestBundle\Model\CollectionResponse;
use Ecentria\Libraries\EcentriaRestBundle\Model\Embedded\EmbeddedInterface;
use Ecentria\Libraries\EcentriaRestBundle\Services\Transaction\TransactionResponseManager;
use FOS\RestBundle\View\View;
use JMS\Serializer\Exception\ValidationFailedException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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
    /**
     * Constructor
     *
     * @param TransactionResponseManager $transactionResponseManager transactionResponseManager
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
     * @param GetResponseForExceptionEvent $event           event
     * @param string                       $name            name
     * @param EventDispatcherInterface     $eventDispatcher eventDispatcher
     *
     * @return void
     */
    public function onKernelException(
        GetResponseForExceptionEvent $event,
        $name,
        EventDispatcherInterface $eventDispatcher
    ) {
        $exception = $event->getException();
        if ($exception instanceof ValidationFailedException) {
            $response = $this->getValidationFailedExceptionResponse($event, $eventDispatcher, $exception);
            if ($response instanceof Response) {
                $event->setResponse($response);
            }

        }
    }

    /**
     * Get correct response for validation failed exception
     *
     * @param GetResponseForExceptionEvent $event           event
     * @param EventDispatcherInterface     $eventDispatcher eventDispatcher
     * @param ValidationFailedException    $exception       exception
     *
     * @return Response|null
     */
    private function getValidationFailedExceptionResponse(
        GetResponseForExceptionEvent $event,
        EventDispatcherInterface $eventDispatcher,
        ValidationFailedException $exception
    ) {
        $event->stopPropagation();
        $request = $event->getRequest();

        $transaction = $request->get('transaction');

        if (!$transaction) {
            return $event->getResponse();
        }

        $data = $request->get(
            $request->get(Alias::DATA)
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
