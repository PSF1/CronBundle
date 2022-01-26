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

namespace VM\Cron;

/**
 * Cron job interface.
 */
interface JobInterface
{

    /**
     * Execute job.
     *
     * @return void
     */
    public function run(): void;
}
