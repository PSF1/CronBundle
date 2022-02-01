<?php

/**
 * This file is part of Cron Bundle.
 *
 * @category bundle
 *
 * @author   Valentín Mari <https://github.com/vmari>
 * @author   Pedro Pelaez <aaaaa976@gmail.com>
 *
 * @license  https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html GPL v2
 *
 * @link     https://github.com/vmari/CronBundle
 */

namespace VM\Cron\Entity;

use Cron\CronExpression;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Lock\LockFactory;
use VM\Cron\CronService;

/**
 * Cron job.
 */
class Cron
{

    private $format;
    private $service;
    private $name;
    private $cacheDir;
    private LockFactory $factory;
    private LoggerInterface $logger;

    /**
     * @param string          $name
     * @param string          $format
     * @param mixed           $service
     * @param string          $cacheDir
     * @param LockFactory     $factory
     * @param LoggerInterface $logger
     */
    public function __construct($name, $format, $service, $cacheDir, LockFactory $factory, LoggerInterface $logger)
    {
        $this->name = $name;
        $this->format = $format;
        $this->service = $service;
        $this->cacheDir = $cacheDir;
        $this->factory = $factory;
        $this->logger = $logger;
    }

    /**
     * Get cron job name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get last execution date.
     *
     * @return \DateTime
     */
    public function getLastRun()
    {
        $filename = $this->getFileCacheName();
        $date = new \DateTime();

        if (!file_exists($filename)) {
            $date->setTimestamp(0);

            return $date;
        }
        $date->setTimestamp(intval(@file_get_contents($filename)));

        return $date;
    }

    /**
     * Update last execution date.
     *
     * @param \DateTime $time
     *
     * @return void
     */
    public function setLastRun(\DateTime $time)
    {
        file_put_contents($this->getFileCacheName(), $time->getTimestamp());
    }

    /**
     * Is this job in time to execute?
     *
     * @return bool
     *
     * @throws Exception
     */
    public function intoFormat()
    {
        $now = new \DateTime('now');

        return ($this->nextRun() <= $now);
    }

    /**
     * Execute cron job if it's in time.
     *
     * @return void
     *
     * @throws Exception
     */
    public function run()
    {
        $now = new \DateTime('now');
        if ($this->nextRun() <= $now) {
            $this->runForced();
        }
    }

    /**
     * Execute cron job now.
     *
     * @return void
     *
     * @throws Exception
     */
    public function runForced()
    {
        $lockHandler = $this->factory->createLock('cron.lock');
        $lockHandler->acquire();

        if ($lockHandler->isAcquired()) {
            $serviceClass = (is_string($this->service) ? $this->service : get_class($this->service));
            try {
                $now = new \DateTime('now');
                CronService::validateCronJobClass($serviceClass);
            } catch (Exception $e) {
                $this->logger->debug(sprintf('Cron job %s fail: %s. Lock released.', $serviceClass, $e->getMessage()));
                $this->setLastRun($now);
                $lockHandler->release();
                throw new Exception(sprintf('%s: %s', $serviceClass, $e->getMessage()));
            }
            // Ensure that the lock will be released.
            try {
                if (is_object($this->service)) {
                    $worker = $this->service;
                } else {
                    $worker = new $this->service();
                }
                $worker->run();
                $this->setLastRun($now);
            } finally {
                $lockHandler->release();
                $this->logger->debug(sprintf('Cron job "%s" executed. Lock released.', $this->getName()));
            }
        }
    }

    /**
     * Get next execution date.
     *
     * @return \DateTime
     *
     * @throws Exception
     */
    public function nextRun()
    {
        $cron = new CronExpression($this->format);

        return $cron->getNextRunDate($this->getLastRun());
    }

    /**
     * Get cache folder root.
     *
     * @return string
     */
    private function getRoot()
    {
        $dirname = $this->cacheDir.'/cron';

        if (!is_dir($dirname)) {
            mkdir($dirname, 0755, true);
        }

        return $dirname;
    }

    /**
     * Get file cache name.
     *
     * @return string
     */
    private function getFileCacheName()
    {
        $serviceClass = (is_string($this->service) ? $this->service : get_class($this->service));
        return $this->getRoot().'/'.md5($this->format.$serviceClass).'.cron';
    }
}
