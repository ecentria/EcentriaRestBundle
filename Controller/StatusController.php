<?php

namespace Ecentria\Libraries\CoreRestBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class StatusController extends FOSRestController
{
    /**
     * Get the status of the application
     *
     * @ApiDoc()
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getStatusAction()
    {
        $data = array('OK','0', 'All related services are available. All systems normal.');

        $view = $this->view($data, 200);

        return $this->handleView($view);
    } // "get_status"     [GET] /status

}