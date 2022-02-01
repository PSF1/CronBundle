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

namespace VM\Cron\EventListener;

use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\Lock\LockFactory;
use VM\Cron\Entity\Cron;

/**
 * Request event listener.
 */
class RequestListener
{
    private LockFactory $factory;
    private $cacheDir;
    /**
     * @var array|bool|float|int|string|null
     */
    private $jobs;
    private LoggerInterface $logger;
    /**
     * @var bool
     */
    private $runOnRequest;

    /**
     * @param ContainerInterface $container
     * @param LockFactory        $factory
     * @param LoggerInterface    $logger
     */
    public function __construct(ContainerInterface $container, LockFactory $factory, LoggerInterface $logger)
    {
        $this->cacheDir = $container->getParameterBag()->get('kernel.cache_dir');

        $cronConfig = $container->getParameter('cron');
        $this->jobs = (isset($cronConfig['jobs'])) ? $cronConfig['jobs'] : [];
        foreach ($this->jobs as &$job) {
            if (isset($job['service'])) {
                // Get service instance with autowiring if configured.
                $job['service'] = $container->get($job['service']);
            }
        }

        $this->runOnRequest = (isset($cronConfig['run_on_request'])) ? $cronConfig['run_on_request'] : false;

        $this->factory = $factory;
        $this->logger = $logger;
    }

    /**
     * After request execution try to execute cron.
     *
     * @param TerminateEvent $event
     *
     * @return void
     *
     * @throws Exception
     */
    public function onKernelTerminate(TerminateEvent $event)
    {
        if (!$this->runOnRequest) {
            return;
        }

        $lockHandler = $this->factory->createLock('cron.request.lock');
        $lockHandler->acquire();

        if (!$lockHandler->isAcquired()) {
            return;
        }

        foreach ($this->jobs as $cron) {
            try {
                $job = new Cron($cron['name'], $cron['format'], $cron['service'], $this->cacheDir, $this->factory, $this->logger);
                $job->run();
            } catch (Exception $e) {
                // Ignore errors.
            }
        }
        $lockHandler->release();
    }
}
