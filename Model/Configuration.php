<?php
/*
 * This file is part of the Ecentria software.
 *
 * (c) 2014, OpticsPlanet, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\CoreRestBundle\Model;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Collection Response
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class Configuration
{
    /**
     * Routes
     *
     * @var ArrayCollection
     */
    private $routes;

    /**
     * Routes setter
     *
     * @param ArrayCollection $routes
     *
     * @return self
     */
    public function setRoutes($routes)
    {
        $this->routes = $routes;
        return $this;
    }

    /**
     * Routes getter
     *
     * @return ArrayCollection
     */
    public function getRoutes()
    {
        return $this->routes;
    }
}

