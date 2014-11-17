<?php
/*
 * This file is part of the Ecentria software.
 *
 * (c) 2014, OpticsPlanet, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\CoreRestBundle\Controller;

use FOS\RestBundle\Controller\Annotations as FOS,
    FOS\RestBundle\Controller\FOSRestController;

use Nelmio\ApiDocBundle\Annotation as Nelmio;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Configuration Controller
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class ConfigurationController extends FOSRestController
{
    /**
     * @FOS\Route(
     *      pattern="configuration"
     * )
     *
     * @Nelmio\ApiDoc(
     *      section="Configuration",
     *      resource=true,
     *      statusCodes={
     *          200="Returned when successful",
     *          500="Returned when system failed"
     *      }
     * )
     *
     * @return JsonResponse
     */
    public function getConfigurationAction()
    {
        $configurationManager = $this->get('ecentria.configuration_manager');
        $configuration = $configurationManager->getConfiguration();
        return $this->view($configuration);
    }
}