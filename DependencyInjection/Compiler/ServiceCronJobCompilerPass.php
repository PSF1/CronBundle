<?php

/**
 * This file is part of Cron Bundle.
 *
 * @category bundle
 *
 * @author   Pedro Pelaez <aaaaa976@gmail.com>
 *
 * @license  https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html GPL v2
 *
 * @link     https://github.com/vmari/CronBundle
 */

namespace VM\Cron\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

use VM\Cron\JobInterface;
use function array_combine;
use function array_keys;
use function array_map;

/**
 * Set public cron job services.
 */
final class ServiceCronJobCompilerPass implements CompilerPassInterface
{

    /**
     * @param ContainerBuilder $container
     *
     * @return void
     */
    public function process(ContainerBuilder $container): void
    {
        $definitions = $container->getDefinitions();
        /** @var Definition $definition */
        foreach ($definitions as &$definition) {
            $class = $definition->getClass();
            if ($class && class_exists($class)) {
                $implements = class_implements($class, true);
                if (in_array(JobInterface::class, $implements)) {
                    $definition->setPublic(true);
                }
            }
        }
        $container->setDefinitions($definitions);
    }
}
