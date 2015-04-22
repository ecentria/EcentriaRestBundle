<?php
/*
 * This file is part of the Ecentria software.
 *
 * (c) 2014, OpticsPlanet, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\CoreRestBundle\Services\Transaction\Handler;

use Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\EntityManager;

use Ecentria\Libraries\CoreRestBundle\Entity\Transaction,
    Ecentria\Libraries\CoreRestBundle\Model\CollectionResponse,
    Ecentria\Libraries\CoreRestBundle\Model\CRUD\CrudEntityInterface,
    Ecentria\Libraries\CoreRestBundle\Services\ErrorBuilder,
    Ecentria\Libraries\CoreRestBundle\Services\NoticeBuilder,
    Ecentria\Libraries\CoreRestBundle\Services\UUID;

use Gedmo\Exception\FeatureNotImplementedException;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * Transaction POST handler
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class TransactionPutHandler extends TransactionPostHandler
{
    /**
     * Supports method
     *
     * @return string
     */
    public function supports()
    {
        return 'PUT';
    }
}
