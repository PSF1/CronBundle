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

namespace VM\Cron\Cache;

use Symfony\Component\HttpKernel\CacheClearer\CacheClearerInterface;

/**
 * Clear cron cache.
 */
class ClearCache implements CacheClearerInterface
{

    /**
     * Clear cache folder.
     *
     * @todo clear all files in cache directory
     *
     * @param string $cacheDir
     *
     * @return void
     */
    public function clear($cacheDir)
    {
        //TODO: clear all files in cache directory
    }
}
