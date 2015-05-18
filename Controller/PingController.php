<?php
/*
 * This file is part of the ecentria group, inc. software.
 *
 * (c) 2015, ecentria
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\CoreRestBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Ping Controller for monitoring purposes
 *
 * @author Ruslan Zavacky <ruslan.zavacky@intexsys.lv>
 */
class PingController extends FOSRestController
{
    /**
     * Get the status of the application
     *
     * @ApiDoc(
     *      section="Monitoring",
     *      statusCodes={
     *          200="Returned when successful"
     *      }
     * )
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getPingAction()
    {
        return $this->view(array('pong'), 200);
    }
}
