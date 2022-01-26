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

namespace VM\Cron;

use Exception;

/**
 * Cron helper service.
 */
class CronService
{

    /**
     * Validate cron job class.
     *
     * @param string $class Job class name.
     *
     * @return bool
     *
     * @throws Exception
     */
    public static function validateCronJobClass($class)
    {
        if (class_exists($class, true)) {
            if (isset(class_implements($class, true)['VM\Cron\JobInterface'])) {
                return true;
            }

            throw new Exception('Worker must implement JobInterface');
        }

        throw new Exception('Worker class not found');
    }
}
