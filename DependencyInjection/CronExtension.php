<?php

/**
 * This file is part of Symfony Cron Bundle.
 *
 * @category bundle
 *
 * @author   Valentín Mari <https://github.com/vmari>
 *
 * @license  https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html GPL v2
 *
 * @link     https://github.com/vmari/CronBundle
 */

namespace VM\Cron\DependencyInjection;

use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

/**
 * Cron extension.
 */
class CronExtension extends Extension
{

    /**
     * Load cron extension.
     *
     * @param array            $configs
     * @param ContainerBuilder $container
     *
     * @return void
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('cron', $config);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
