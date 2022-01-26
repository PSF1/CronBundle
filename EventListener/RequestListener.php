<?php

/**
 * This file is part of Symfony Cron Bundle.
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

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\Lock\LockFactory;
use VM\Cron\Entity\Cron;

/**
 * Request event listener.
 */
class RequestListener
{

    private ContainerInterface $container;
    private LockFactory $factory;

    /**
     * @param ContainerInterface $container
     * @param LockFactory        $factory
     */
    public function __construct(ContainerInterface $container, LockFactory $factory)
    {
        $this->container = $container;
        $this->factory = $factory;
    }

    /**
     * After request execution try to execute cron.
     *
     * @param TerminateEvent $event
     *
     * @return void
     *
     * @throws \Exception
     */
    public function onKernelTerminate(TerminateEvent $event)
    {
        $lockHandler = $this->factory->createLock('cron.lock');
        $lockHandler->acquire();

        if ($lockHandler->isAcquired()) {
            $crons = $this->container->getParameter('cron');

            foreach ($crons as $cron) {
                $job = new Cron($cron['format'], $cron['service'], $this->container);
                $job->run();
            }
            $lockHandler->release();
        }
    }
}
