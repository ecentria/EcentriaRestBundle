<?php
/*
 * This file is part of the Ecentria software.
 *
 * (c) 2014, OpticsPlanet, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\CoreRestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Ecentria\Libraries\CoreRestBundle\Validator\Constraints as EcentriaAssert;
use Doctrine\ORM\Mapping\MappedSuperclass;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;

/**
 * @Hateoas\Relation(
 *      "service-transaction",
 *      href = @Hateoas\Route(
 *          "get_transaction",
 *          parameters = {
 *              "id" = "expr(object.getTransaction().getId())"
 *          },
 *          absolute = true
 *      ),
 *      embedded = "expr(object.getTransaction())"
 * )
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class NullEntity
{
    /**
     * @Serializer\Exclude
     */
    private $transaction;

    public function getTransaction()
    {
        return $this->transaction;
    }

    public function setTransaction($transaction)
    {
        $this->transaction = $transaction;
    }
}
