<?php
/*
 * This file is part of the Ecentria software.
 *
 * (c) 2014, OpticsPlanet, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\CoreRestBundle\Controller\FOSRest;

use FOS\RestBundle\Controller\FOSRestController as BaseFOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;

/**
 * FOS Rest Controller
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 *
 */
class FOSRestController extends BaseFOSRestController implements ClassResourceInterface
{
    /**
     * {@inheritdoc}
     */
    protected function view($data = null, $statusCode = null, array $headers = array())
    {
        $errorHandler = $this->get('ecentria.error.response.handler');

        if ($data instanceof \Exception || is_null($data)) {
            $errorHandler->handle($data);
            $data = $errorHandler->getData();
            $statusCode = $errorHandler->getStatusCode();
        }

        return parent::view($data, $statusCode, $headers);
    }
}