<?php
/*
 * This file is part of the ecentria group, inc. software.
 *
 * (c) 2015, ecentria group, inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\CoreRestBundle\Services;

use Doctrine\Common\Collections\ArrayCollection;
use Ecentria\Libraries\CoreRestBundle\Model\Configuration;

use Symfony\Component\Routing\Route,
    Symfony\Component\Routing\Router;

/**
 * Configuration manager
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class ConfigurationManager
{
    /**
     * Router
     *
     * @var Router
     */
    private $router;

    /**
     * Constructor
     *
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Get configuration
     *
     * @return Configuration
     */
    public function getConfiguration()
    {
        $configuration = new Configuration();
        $configuration->setRoutes($this->getRoutes());
        return $configuration;
    }

    /**
     * Getting routes
     *
     * @return ArrayCollection
     */
    private function getRoutes()
    {
        $routeCollection = $this->router->getRouteCollection();
        $routes = new ArrayCollection();
        foreach ($routeCollection as $name => $route) {
            if ($route instanceof Route) {
                $options = $route->getOptions();
                if (isset($options['expose']) && $options['expose'] === true) {
                    $methods = $route->getMethods();
                    $routes->set(
                        $name,
                        array(
                            'method'  => reset($methods),
                            'pattern' => $route->getPath(),
                        )
                    );
                }
            }
        }
        return $routes;
    }
}
