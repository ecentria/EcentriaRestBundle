<?php
/*
 * This file is part of the Ecentria software.
 *
 * (c) 2014, OpticsPlanet, Inc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\CoreRestBundle;

use Ecentria\Libraries\CoreRestBundle\DependencyInjection\Compiler\TransactionHandlerPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Ecentria libraries core rest bundle
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 *
 */
class EcentriaLibrariesCoreRestBundle extends Bundle
{
    /**
     * Build
     *
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new TransactionHandlerPass(), PassConfig::TYPE_OPTIMIZE);
        parent::build($container);
    }
}
