<?php
/*
* This file is part of the Ecentria software.
*
* (c) 2015, OpticsPlanet, Inc
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Ecentria\Libraries\CoreRestBundle\Model\RequestCreator;

use Doctrine\Common\Collections\ArrayCollection;
use Ecentria\Libraries\CoreRestBundle\Services\RequestCreator;

/**
 * Creator strategy interface
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
interface RequestCreatorStrategyInterface
{
    /**
     * Apply
     *
     * @param RequestCreator  $creator Creator
     * @param ArrayCollection $data    Data
     *
     * @return bool
     */
    public function apply(RequestCreator $creator, ArrayCollection $data);

    /**
     * Get info messages
     *
     * @return ArrayCollection
     */
    public function getMessages();

    /**
     * Create
     *
     * @return RequestCreatorStrategyInterface
     */
    public static function create();
}
