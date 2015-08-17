<?php
/*
 * This file is part of the ecentria group, inc. software.
 *
 * (c) 2015, ecentria group, inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Ecentria\Libraries\EcentriaRestBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ecentria\Libraries\EcentriaRestBundle\Event\Events;
use Symfony\Component\EventDispatcher\Event;
use Ecentria\Libraries\EcentriaRestBundle\Event\StatusCheckEvent;

/**
 * Status Controller for monitoring purposes
 *
 * @author Ruslan Zavacky <ruslan.zavacky@intexsys.lv>
 */
class StatusController extends FOSRestController
{
    /**
     * Get the status of the application
     *
     * @ApiDoc(
     *      section="Monitoring",
     *      statusCodes={
     *          200="Returned when successful",
     *          500="Some services do not work"
     *      }
     * )
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getStatusAction()
    {
        $dispatcher = $this->get('event_dispatcher');
        $event = new StatusCheckEvent();
        $dispatcher->dispatch(Events::STATUS_CHECK, $event);
        if ($event->getState() == StatusCheckEvent::STATE_OK) {
            return $this->view(
                [$event->getState(), $event->getStateCode(), []],
                200
            );
        } else {
            return $this->view(
                [$event->getState(), $event->getStateCode(), $event->getExceptionMessages()],
                $event->getState() == StatusCheckEvent::STATE_WARNING ? 200 : 500
            );
        }
    }
}
