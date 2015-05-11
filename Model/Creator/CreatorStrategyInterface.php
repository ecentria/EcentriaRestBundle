<?php
/*
* This file is part of the Ecentria software.
*
* (c) 2015, OpticsPlanet, Inc
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Ecentria\Libraries\CoreRestBundle\Model\Creator;

use Doctrine\Common\Collections\ArrayCollection;
use Ecentria\Libraries\CoreRestBundle\Services\Creator;

/**
 * Creator strategy interface
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
interface CreatorStrategyInterface
{
    /**
     * Apply
     *
     * @param Creator         $creator Creator
     * @param ArrayCollection $data    Data
     *
     * @return bool
     */
    public function apply(Creator $creator, ArrayCollection $data);

    /**
     * Create
     *
     * @return CreatorStrategyInterface
     */
    public static function create();
}
