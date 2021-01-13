<?php

declare(strict_types=1);

namespace Abryb\ConsoleHandler\Section\ProgressBar;

/**
 * @author Błażej Rybarkiewicz <b.rybarkiewicz@gmail.com>
 */
class RegexProgressBarSection extends AbstractProgressBarSection
{
    /**
     * @var string
     */
    private $startRegex;
    /**
     * @var string
     */
    private $advanceRegex;
    /**
     * @var string
     */
    private $finishRegex;
    /**
     * @var string
     */
    private $title;

    /**
     * RegexProgressBarHandler constructor.
     *
     * @param string $startRegex   = "#Something (?<maxCount>\d+)#"
     * @param string $advanceRegex = "#Something (?<step>\d+)#" or just "#Something#" for step = 1
     * @param string $finishRegex  = "#Something#"
     */
    public function __construct(string $startRegex, string $advanceRegex, string $finishRegex, string $title = '')
    {
        $this->startRegex   = $startRegex;
        $this->advanceRegex = $advanceRegex;
        $this->finishRegex  = $finishRegex;
        $this->title        = $title;
    }

    /**
     * {@inheritdoc}
     */
    public function getMaxCount(array $record): int
    {
        preg_match($this->startRegex, $record['message'], $matches);
        $maxCount = $matches['maxCount'] ?? null;

        return $maxCount ? (int) $maxCount : 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getAdvance(array $record): int
    {
        $is      = preg_match($this->advanceRegex, $record['message'], $matches);
        $advance = $matches['advance'] ?? (int) $is;

        return $advance ? (int) $advance : 0;
    }

    /**
     * {@inheritdoc}
     */
    public function isFinish(array $record): bool
    {
        return (bool) preg_match($this->finishRegex, $record['message']);
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * {@inheritdoc}
     */
    protected function isStart(array $record): bool
    {
        return (bool) preg_match($this->startRegex, $record['message'], $matches);
    }
}
