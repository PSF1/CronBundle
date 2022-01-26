## Installation

1. Download CronBundle using composer
2. Enable the Bundle, if required
3. Define cron jobs in your config

### Step 1: Download CronBundle using composer

Add CronBundle by running the command:

``` bash
$ php composer.phar require valentinmari/cron-bundle "dev-master"
```

Composer will install the bundle to your project's `vendor/valentinmari` directory.

### Step 2: Enable the bundle

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
cron:
  - { name: 'Test job 1', format: '*/1 * * * *', service: 'App\Cron\TestJob1' }
  - { name: 'Test job 2', format: '*/1 * * * *', service: 'App\Cron\TestJob2' }
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
