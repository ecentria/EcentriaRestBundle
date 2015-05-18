<?php
/*
 * This file is part of the ecentria group, inc. software.
 *
 * (c) 2015, ecentria group, inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\CoreRestBundle\EventListener;

use Ecentria\Libraries\CoreRestBundle\Model\CollectionResponse;
use Ecentria\Libraries\CoreRestBundle\Model\Embedded\EmbeddedInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

/**
 * Embedded response listener
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class ResponseListener
{
    /**
     * Let's embed our response
     *
     * @param GetResponseForControllerResultEvent $event Event
     *
     * @return void
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $request = $event->getRequest();
        $view = $event->getControllerResult();

        if (!$view instanceof View) {
            return;
        }

        $embedded = filter_var($request->get('_embedded'), FILTER_VALIDATE_BOOLEAN);
        $data = $view->getData();

        if ($data instanceof EmbeddedInterface && $data->showAssociations() === null) {
            $data->setShowAssociations($embedded);
        }

        if ($data instanceof CollectionResponse) {
            $data->setInheritedShowAssociations($embedded);
        }
    }
}
