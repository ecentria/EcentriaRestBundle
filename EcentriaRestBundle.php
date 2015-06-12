<?php
/*
 * This file is part of the ecentria group, inc. software.
 *
 * (c) 2015, ecentria group, inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\EcentriaRestBundle;

use Ecentria\Libraries\EcentriaRestBundle\DependencyInjection\Compiler\TransactionHandlerPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Ecentria libraries core rest bundle
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 *
 */
class EcentriaRestBundle extends Bundle
{
    /**
     * Build
     *
     * @param ContainerBuilder $container
     *
     * @return void
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new TransactionHandlerPass(), PassConfig::TYPE_OPTIMIZE);
        parent::build($container);
    }
}
