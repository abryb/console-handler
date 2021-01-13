<?php

declare(strict_types=1);

namespace Abryb\ConsoleHandler\Section\Log;

use Abryb\ConsoleHandler\SectionInterface;
use Monolog\Formatter\FormatterInterface;
use Monolog\Logger;
use Symfony\Bridge\Monolog\Formatter\ConsoleFormatter;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Błażej Rybarkiewicz <b.rybarkiewicz@gmail.com>
 */
class WriteLogSection implements SectionInterface
{
    /**
     * @var OutputInterface
     */
    protected $output;
    private $verbosityLevelMap = [
        OutputInterface::VERBOSITY_QUIET        => Logger::ERROR,
        OutputInterface::VERBOSITY_NORMAL       => Logger::WARNING,
        OutputInterface::VERBOSITY_VERBOSE      => Logger::NOTICE,
        OutputInterface::VERBOSITY_VERY_VERBOSE => Logger::INFO,
        OutputInterface::VERBOSITY_DEBUG        => Logger::DEBUG,
    ];

    /**
     * @var array = [
     *            'format' => ConsoleFormatter::SIMPLE_FORMAT,
     *            'date_format' => ConsoleFormatter::SIMPLE_DATE,
     *            'colors' => true,
     *            'multiline' => false,
     *            'level_name_format' => '%-9s',
     *            'ignore_empty_context_and_extra' => true,
     *            ]
     */
    private $consoleFormatterOptions;

    /**
     * @var FormatterInterface
     */
    private $formatter;

    public function __construct(
        array $verbosityLevelMap = [],
        array $consoleFormatterOptions = []
    ) {
        if ($verbosityLevelMap) {
            $this->verbosityLevelMap = $verbosityLevelMap;
        }
        $this->consoleFormatterOptions = $consoleFormatterOptions;
    }

    public function handle(array $record)
    {
        if ($this->shouldWrite($record)) {
            $message = $this->formatter->format($record);
            $this->write($message);
        }
    }

    public function setOutput(OutputInterface $output)
    {
        $this->output    = $output;
        $this->formatter = new ConsoleFormatter(array_replace([
            'colors'    => $this->output->isDecorated(),
            'multiline' => OutputInterface::VERBOSITY_DEBUG <= $this->output->getVerbosity(),
        ], $this->consoleFormatterOptions));
    }

    protected function write($message)
    {
        $message = trim($message, "\\ \t\n\r\0\x0B");
        $this->output->writeln($message);
    }

    private function shouldWrite(array $record)
    {
        if (!$this->output) {
            return false;
        }
        $verbosity = $this->output->getVerbosity();
        $minLevel  = $this->verbosityLevelMap[$verbosity] ?? Logger::DEBUG;

        return $record['level'] >= $minLevel;
    }
}
