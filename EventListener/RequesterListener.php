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

use \Symfony\Component\HttpFoundation\Request;
use \Gedmo\Blameable\BlameableListener;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Ecentria\Libraries\EcentriaRestBundle\Model\Requester;

/**
 * Requester Listener - Listens to the request and sets the blameable user value to the requester
 *
 * @author Ryan Wood <ryan.wood@opticsplanet.com>
 */
class RequesterListener
{
    /**
     * Blameable Listener
     *
     * @var BlameableListener
     */
    private $blameableListener;

    /**
     * Constructor
     *
     * @param BlameableListener $blameableListener
     */
    public function __construct(BlameableListener $blameableListener)
    {
        $this->blameableListener = $blameableListener;
    }

    /**
     * On kernel request action
     *
     * @param GetResponseEvent $event Event object
     *
     * @return void
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $requester = new Requester($request);
        if ($requester->hasUsername()) {
            $this->blameableListener->setUserValue($requester);
        }
    }
}
