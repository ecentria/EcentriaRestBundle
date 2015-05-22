<?php
/*
 * This file is part of the ecentria group, inc. software.
 *
 * (c) 2015, ecentria group, inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\EcentriaRestBundle\DependencyInjection\Compiler;

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
     * {@inheritDoc}
     *
     * @param ContainerBuilder $container
     *
     * @return void
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('ecentria.api.transaction.manager')) {
            return;
        }

        $handler = $container->getDefinition('ecentria.api.transaction.manager');

        $handlers = array();
        foreach ($container->findTaggedServiceIds('ecentria.api.tag.transaction_handler') as $id => $attributes) {
            $handlers[] = $container->getDefinition($id);
        }
        $handler->replaceArgument(0, $handlers);
        $container->setDefinition('ecentria.api.transaction.manager', $handler);
    }
}
