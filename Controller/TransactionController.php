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

use Ecentria\Libraries\CoreRestBundle\Controller\FOSRest\FOSRestController;
use Ecentria\Libraries\CoreRestBundle\Entity\Transaction;
use FOS\RestBundle\Controller\Annotations\Route;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Contact Controller
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class TransactionController extends FOSRestController
{
    /**
     * @Route(
     *      pattern="transaction-service/{id}",
     *      requirements = {
     *          "id" = ".+?"
     *      }
     * )
     *
     * @ParamConverter(
     *      "transactionEntity",
     *      class="Ecentria\Libraries\CoreRestBundle\Entity\Transaction",
     *      converter = "ecentria.doctrine_param_converter"
     * )
     *
     * @ApiDoc(
     *      section="Transaction",
     *      resource=true,
     *      statusCodes={
     *          200="Returned when successful",
     *          404="Returned when not found",
     *          500="Returned when system failed"
     *      }
     * )
     *
     * @param Transaction $transactionEntity
     * @return JsonResponse
     */
    public function getAction(Transaction $transactionEntity)
    {
        return $this->view($transactionEntity);
    }
}