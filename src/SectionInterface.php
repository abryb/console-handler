<?php

declare(strict_types=1);

namespace Abryb\ConsoleHandler;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Błażej Rybarkiewicz <b.rybarkiewicz@gmail.com>
 */
interface SectionInterface
{
    public function handle(array $record);

    public function setOutput(OutputInterface $output);
}
