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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use VM\Cron\CronService;
use VM\Cron\Entity\Cron;

/**
 * Cron job list.
 */
class CronJobRunCommand extends Command
{
    protected static $defaultName = 'cron:job:run';
    private array $jobs;
    private $cacheDir;

    /**
     * @param array  $jobs
     * @param string $cacheDir
     */
    public function __construct(array $jobs, $cacheDir)
    {
        parent::__construct(self::$defaultName);
        $this->jobs = $jobs;
        $this->cacheDir = $cacheDir;
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Run cron jobs')
            ->addArgument('name', InputArgument::OPTIONAL, 'Job name to run, leave empty to run all')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Execute the cron job now')
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
        $jobName = $input->getArgument('name');
        $force = $input->getOption('force');

        foreach ($this->jobs as $job) {
            if ($jobName && $job['name'] !== $jobName) {
                continue;
            }
            $name = $job['name'];
            $io->title($name);
            $job = new Cron($job['name'], $job['format'], $job['service'], $this->cacheDir);
            $executed = true;
            try {
                if ($force) {
                    $job->runForced();
                } else {
                    if (!$job->intoFormat()) {
                        $io->warning(sprintf('Job "%s" it\'s not in time to execute it', $name));
                        $executed = false;
                    }
                    $job->run();
                }
                if ($executed) {
                    $io->success(sprintf('Job "%s" executed', $name));
                }
            } catch (Exception $e) {
                $io->error($e->getMessage());
            }
        }

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
