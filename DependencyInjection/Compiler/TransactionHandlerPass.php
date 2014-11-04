<?php
/*
 * This file is part of the Ecentria software.
 *
 * (c) 2014, OpticsPlanet, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\CoreRestBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Transaction handler pass
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class TransactionHandlerPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('ecentria.transaction_response_manager')) {
            return;
        }

        $handler = $container->getDefinition('ecentria.transaction_response_manager');

        $handlers = array();
        foreach ($container->findTaggedServiceIds('ecentria.transaction_method_handler') as $id => $attributes) {
            $handlers[] = $container->getDefinition($id);
        }
        $handler->replaceArgument(0, $handlers);
        $container->setDefinition('ecentria.transaction_response_manager', $handler);
    }
}