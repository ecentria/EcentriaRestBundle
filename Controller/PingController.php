<?php

namespace Ecentria\Libraries\CoreRestBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class PingController extends FOSRestController
{
    /**
     * Get the status of the application
     *
     * @ApiDoc()
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getPingAction()
    {
        $data = array('pong');

        $view = $this->view($data, 200);

        return $this->handleView($view);
    } // "get_ping"     [GET] /ping
}