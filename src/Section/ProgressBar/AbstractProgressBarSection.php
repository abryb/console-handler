<?php

declare(strict_types=1);

namespace Abryb\ConsoleHandler\Section\ProgressBar;

use Abryb\ConsoleHandler\SectionInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Błażej Rybarkiewicz <b.rybarkiewicz@gmail.com>
 */
abstract class AbstractProgressBarSection implements SectionInterface
{
    /**
     * @var OutputInterface|null
     */
    private $output;

    /**
     * @var ProgressBar|null
     */
    private $progressBar;

    public function handle(array $record)
    {
        if (!$this->progressBar && $this->isStart($record)) {
            $maxCount = $this->getMaxCount($record);
            if ($maxCount > 0) {
                $this->progressBar = $this->createProgressBar($this->getMaxCount($record), $this->getTitle());
                $this->progressBar->start();
            }

            return;
        }
        if ($this->progressBar) {
            if ($this->isFinish($record)) {
                $this->progressBar->finish();

                return;
            }
            $this->progressBar->advance($this->getAdvance($record));
        }
    }

    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
    }

    abstract protected function isStart(array $record): bool;

    abstract protected function getMaxCount(array $record): int;

    abstract protected function getAdvance(array $record): int;

    abstract protected function isFinish(array $record): bool;

    abstract protected function getTitle(): string;

    protected function createProgressBar(?int $maxCount, string $title)
    {
        $bar =  new ProgressBar($this->output, $maxCount);

        $format = self::initFormats()[$this->determineBestFormat($maxCount)];

        $format = sprintf('%s <comment>%s</comment>', $format, $title);

        $bar->setFormat($format);

        return $bar;
    }

    protected static function initFormats(): array
    {
        return [
            'normal'       => ' %current%/%max% [%bar%] %percent:3s%%',
            'normal_nomax' => ' %current% [%bar%]',

            'verbose'       => ' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%',
            'verbose_nomax' => ' %current% [%bar%] %elapsed:6s%',

            'very_verbose'       => ' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s%',
            'very_verbose_nomax' => ' %current% [%bar%] %elapsed:6s%',

            'debug'       => ' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%',
            'debug_nomax' => ' %current% [%bar%] %elapsed:6s% %memory:6s%',
        ];
    }

    protected function determineBestFormat(int $max): string
    {
        switch ($this->output->getVerbosity()) {
            // OutputInterface::VERBOSITY_QUIET: display is disabled anyway
            case OutputInterface::VERBOSITY_VERBOSE:
                return $max ? 'verbose' : 'verbose_nomax';
            case OutputInterface::VERBOSITY_VERY_VERBOSE:
                return $max ? 'very_verbose' : 'very_verbose_nomax';
            case OutputInterface::VERBOSITY_DEBUG:
                return $max ? 'debug' : 'debug_nomax';
            default:
                return $max ? 'normal' : 'normal_nomax';
        }
    }
}
