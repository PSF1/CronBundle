<?php

/**
 * This file is part of Cron Bundle.
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

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use VM\Cron\Console\CronJobListCommand;
use VM\Cron\Console\CronJobRunCommand;

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

        // CronJobListCommand console command.
        if (class_exists(Application::class)) {
            $container->register(CronJobListCommand::class)
                ->setArgument(0, $container->getParameter('cron'))
                ->addTag('console.command', ['command' => CronJobListCommand::getDefaultName()])
            ;
        }
        // CronJobRunCommand console command.
        if (class_exists(Application::class)) {
            $container->register(CronJobRunCommand::class)
                ->setAutowired(true)
                ->setArgument(0, $container->getParameter('cron'))
                ->setArgument(1, $container->getParameterBag()->get('kernel.cache_dir'))
                ->addTag('console.command', ['command' => CronJobRunCommand::getDefaultName()])
            ;
        }
    }
}
