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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configure configuration tree.
 */
class Configuration implements ConfigurationInterface
{

    /**
     * Build tree.
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('cron');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->prototype('array')
                ->children()
                    ->scalarNode('format')->end()
                    ->scalarNode('service')->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
