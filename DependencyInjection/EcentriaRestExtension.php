<?php
/*
 * This file is part of the ecentria group, inc. software.
 *
 * (c) 2015, ecentria group, inc.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ecentria\Libraries\EcentriaRestBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 *
 * @author Sergey Chernecov <sergey.chernecov@intexsys.lv>
 */
class EcentriaRestExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
        $providedType = $config['transaction_storage'];
        $log404 = isset($config['log_404_as_warning']) ? $config['log_404_as_warning'] : false;
        $storageType = 'ecentria.api.transaction.storage.' . $providedType;
        $container->setParameter(
            'ecentria_rest.transaction_storage',
            $storageType
        );
        $container->setParameter('ecentria_rest.log_404_as_warning',
            $log404);
    }

    /**
     * {@inheritDoc}
     */
    public function getAlias()
    {
        return 'ecentria_rest';
    }
}
