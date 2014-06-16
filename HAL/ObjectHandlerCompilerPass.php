<?php

namespace Ecentria\Libraries\CoreRestBundle\HAL;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class ObjectHandlerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('ecentria_libraries_core_rest.object_handler_chain')) {
            return;
        }

        $definition = $container->getDefinition(
            'ecentria_libraries_core_rest.object_handler_chain'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'ecentria_libraries_core_rest.object_handler'
        );

        foreach ($taggedServices as $id => $tagAttributes) {
            foreach($tagAttributes AS $attributes) {
                $definition->addMethodCall(
                    'addObjectHandler',
                    array(new Reference($id), $attributes['alias'])
                );
            }
        }
    }
}