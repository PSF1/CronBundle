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
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Cron job.
 */
class Cron
{

    private $format;
    private $service;
    private $container;
    private $name;

    /**
     * @param string             $name
     * @param string             $format
     * @param string             $service
     * @param ContainerInterface $container
     */
    public function __construct($name, $format, $service, ContainerInterface $container)
    {
        $this->name = $name;
        $this->format = $format;
        $this->service = $service;
        $this->container = $container;
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
     * Execute cron job.
     *
     * @return void
     *
     * @throws \Exception
     */
    public function run()
    {
        $now = new \DateTime('now');
        if ($this->nextRun() <= $now) {
            $this->container->get($this->service)->run();
            $this->setLastRun($now);
        }
    }

    /**
     * Get next execution date.
     *
     * @return \DateTime
     *
     * @throws \Exception
     */
    public function nextRun()
    {
        $cron = CronExpression::factory($this->format);

        return $cron->getNextRunDate($this->getLastRun());
    }

    /**
     * Get cache folder root.
     *
     * @return string
     */
    private function getRoot()
    {
        $env = $this->container->get('kernel')->getEnvironment();

        $dirname = '../app/cache/'.$env.'/cron';

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
        return $this->getRoot().'/'.md5($this->format.$this->service).'.cron';
    }
}
