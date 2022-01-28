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

declare(strict_types=1);

namespace VM\Cron\Console;

use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use VM\Cron\CronService;

/**
 * Cron job list.
 */
class CronJobListCommand extends Command
{
    protected static $defaultName = 'cron:job:list';
    private array $jobs;

    /**
     * @param array $cronConfig
     */
    public function __construct(array $cronConfig)
    {
        parent::__construct(self::$defaultName);
        $this->jobs = (isset($cronConfig['jobs'])) ? $cronConfig['jobs'] : [];
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setDescription('List cron jobs')
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $table = $io->createTable()
            ->setHeaders(['Job', 'Format', 'Worker', 'Status']);
        foreach ($this->jobs as $job) {
            $job[] = $this->validateService($job['service']);
            $table->addRow(array_values($job));
        }
        $table->render();

        return Command::SUCCESS;
    }

    /**
     * Get cron job status.
     *
     * @param string $class
     *
     * @return string
     */
    protected function validateService($class): string
    {
        try {
            CronService::validateCronJobClass($class);

            return 'Ready';
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
