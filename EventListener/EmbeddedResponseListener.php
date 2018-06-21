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

use Ecentria\Libraries\EcentriaRestBundle\Services\Embedded\EmbeddedManager;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

/**
 * Embedded response listener
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class EmbeddedResponseListener
{
    /**
     * Embedded manager
     *
     * @var EmbeddedManager
     */
    protected $embeddedManager;

    /**
     * Constructor
     *
     * @param EmbeddedManager $embeddedManager
     */
    public function __construct(EmbeddedManager $embeddedManager)
    {
        $this->embeddedManager = $embeddedManager;
    }

    /**
     * Setting embedded serialization groups for current response
     *
     * @param GetResponseForControllerResultEvent $event
     *
     * @return void
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $view = $event->getControllerResult();
        if (!$view instanceof View) {
            return;
        }

        $groups = $this->embeddedManager->generateGroups($event->getRequest());
        $view->getContext()->setGroups($groups);
    }
}
