This bundle it's a fork from '[valentinmari/cron-bundle](https://github.com/vmari/CronBundle)', original code by Valentin Mari.

## Installation

1. Download CronBundle using composer
2. Enable the Bundle, if required
3. Define cron jobs in your config

### Step 1: Download CronBundle using composer

Add CronBundle by running the command:

``` bash
$ php composer.phar require psf1/cron-bundle "dev-master"
```

Composer will install the bundle to your project's `vendor/psf1` directory.

### Step 2: Enable the bundle

If you are using Flex this step it's not required.

Enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new CronBundle\CronBundle(),
    );
}
```

### Step 3: Define cron jobs in your config

Your site is ready to run cron jobs. Now, write them in a new file named 
`cron.yml` in your config folder.

```yaml
#config/packages/cron.yaml

# Cron jobs.
cron:
  jobs:
    - { name: 'Test_job_1', format: '*/1 * * * *', service: 'App\Cron\TestJob1' }
    - { name: 'Test_job_2', format: '*/1 * * * *', service: 'App\Cron\TestJob2' }

  # Uncomment to execute jobs after each user request.
  # run_on_request: true
```

The format is like Cron, from Unix. You must define a job class, that must
implement `JobInterface` and redefine the run() method.
Inside run() you can put your Job and do anything you want. You can inject things
in your job class too.

```php
// src/Cron/YourJob.php
namespace AppBundle\Services;
use CronBundle\JobInterface;

class YourJob implements JobInterface
{
    public function run(){
        // Do your stuff.
    }
}
```

## Usage

This bundle allows two work methods.

- Run cron jobs in each request, if it's the time. Note that you must define job 
format with enough time to not duplicate executions.
- Run cron jobs by console command.

### Console commands
- **cron:job:list**: List all defined cron jobs with status.
- **cron:job:run**: Run cron jobs.
