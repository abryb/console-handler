<?php

declare(strict_types=1);

namespace Abryb\ConsoleHandler\Section\Log;

use Symfony\Component\Console\Output\ConsoleSectionOutput;

/**
 * @author Błażej Rybarkiewicz <b.rybarkiewicz@gmail.com>
 */
class RotateLogSection extends WriteLogSection
{
    /**
     * @var int
     */
    private $numberOfLines;

    /**
     * @var string[]
     */
    private $messages = [];

    public function __construct(array $verbosityLevelMap = [], array $consoleFormatterOptions = [], int $numberOfLines = 15)
    {
        parent::__construct($verbosityLevelMap, $consoleFormatterOptions);
        $this->numberOfLines = $numberOfLines;
    }

    protected function write($message)
    {
        $this->pushMessage($message);
        $this->clearOutput();
        $this->flushMessages();
    }

    private function clearOutput()
    {
        if ($this->output instanceof ConsoleSectionOutput) {
            $this->output->clear();
        } else {
            // move to the beginning
            $this->output->write("\x1b[0G");
            // move up if needed
            if ($this->numberOfLines > 1) {
                $this->output->write("\x1b[%dA", (bool) $this->numberOfLines - 1);
            }
            $this->output->write("\x1b[0J");
        }
    }

    private function flushMessages()
    {
        foreach ($this->messages as $message) {
            parent::write($message);
        }
    }

    private function pushMessage(string $message)
    {
        array_push($this->messages, $message);
        if (count($this->messages) > $this->numberOfLines) {
            array_shift($this->messages);
        }
    }
}
