<?php

namespace Ecentria\Libraries\CoreRestBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Ecentria\Libraries\CoreRestBundle\HAL\ObjectHandlerCompilerPass;

class EcentriaLibrariesCoreRestBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ObjectHandlerCompilerPass());
    }
}
