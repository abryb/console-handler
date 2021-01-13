### Installation

##### Install package
```shell
composer require abryb/console-handler
```

##### Register service
```yaml
# config/services.yaml
Abryb\ConsoleHandler\ConsoleHandler:
    decorates: monolog.handler.console
    class: Abryb\ConsoleHandler\ConsoleHandler
```

##### Add progress bar based on application logs

```php
<?php

namespace App\Command;


use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Abryb\ConsoleHandler\ConsoleHandler;
use Abryb\ConsoleHandler\ConsoleHandlerAwareCommand;
use Abryb\ConsoleHandler\Section\Log\RotateLogSection;
use Abryb\ConsoleHandler\Section\ProgressBar\RegexProgressBarSection;

class MyCommand extends Command implements ConsoleHandlerAwareCommand
{
    private $logger;
    
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        parent::__construct();
    }

    public function configureConsoleHandler(ConsoleHandler $consoleHandler)
    {
        $consoleHandler->setSections([
            new RotateLogSection(), 
            new RegexProgressBarSection(
                '#My loop with (?<maxCount>\\d+) items#', // start regex
                '#Loop item \d+', // advance regex
                '#Loop finished#', // finish regex
                'Foo bar' // name of progress bar
            ),
        ]);        
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Do my very long job with loop ...
        $this->logger->debug('My loop with 1000 items');
        for ($i = 0; $i < 1000; $i++) {
            usleep(500);
            $this->logger->debug("Loop item {$i}");
        }
        $this->logger->debug("Loop finished");
        
        return 0;
    }    
}
