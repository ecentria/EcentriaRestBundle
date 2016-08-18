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

use FOS\RestBundle\Controller\Annotations as FOS,
    FOS\RestBundle\Controller\FOSRestController,
    FOS\RestBundle\Routing\ClassResourceInterface,
    FOS\RestBundle\View\View;

use Nelmio\ApiDocBundle\Annotation as Nelmio;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Sensio,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Symfony\Component\HttpFoundation\Request;

use Ecentria\Libraries\EcentriaRestBundle\Services\Transaction\Storage\Doctrine;

/**
 * Contact Controller
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class TransactionController extends FOSRestController implements ClassResourceInterface
{
    /**
     * Get transaction
     *
     * @param Request $request Request instance
     * @param string  $id      Transaction Id
     *
     * @FOS\Route(
     *      path="transaction-service/{id}",
     *      requirements = {
     *          "id" = ".+?"
     *      }
     * )
     *
     * @Nelmio\ApiDoc(
     *      section="Transaction",
     *      resource=true,
     *      statusCodes={
     *          200="Returned when successful",
     *          404="Returned when not found",
     *          500="Returned when system failed"
     *      }
     * )
     *
     * @return View
     */
    public function getAction(Request $request, $id)
    {
        /** @var Doctrine $doctrineStorage */
        $doctrineStorage = $this->get('ecentria.api.transaction.storage.doctrine');
        $transaction = $doctrineStorage->read($id);

        return $this->view($transaction);
    }
}
