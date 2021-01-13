<?php

declare(strict_types=1);

namespace Abryb\ConsoleHandler;

use Abryb\ConsoleHandler\Section\Log\WriteLogSection;
use Monolog\Formatter\FormatterInterface;
use Monolog\Handler\HandlerInterface;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author Błażej Rybarkiewicz <b.rybarkiewicz@gmail.com>
 */
class ConsoleHandler implements HandlerInterface, EventSubscriberInterface
{
    /**
     * @var OutputInterface|null
     */
    private $output;

    /**
     * @var SectionInterface[]
     */
    private $sections;

    /**
     * @var OutputInterface[]
     */
    private $sectionOutputs;

    /**
     * @var FormatterInterface
     */
    private $_formatter;

    /**
     * @var callable[]
     */
    private $processors = [];

    /**
     * ConsoleHandler constructor.
     *
     * @param SectionInterface[] $sections
     */
    public function __construct(?OutputInterface $output = null, ?array $sections = null) {
        if (null === $sections) {
            $sections = [new WriteLogSection()];
        }
        foreach ($sections as $section) {
            $this->pushSection($section);
        }
        if ($output) {
            $this->setOutput($output);
        }
        $this->_formatter = new class() implements FormatterInterface {
            public function format(array $record) { return null; }

            public function formatBatch(array $records) { return array_map([$this, 'format'], $records); }
        };
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ConsoleEvents::COMMAND   => ['onCommand', 255],
            ConsoleEvents::TERMINATE => ['onTerminate', -255],
        ];
    }

    /**
     * Before a command is executed, the handler gets activated and the console output
     * is set in order to know where to write the logs.
     */
    public function onCommand(ConsoleCommandEvent $event)
    {
        $this->setOutput($event->getOutput());
        $command = $event->getCommand();
        if ($command instanceof ConsoleHandlerAwareCommand) {
            $command->configureConsoleHandler($this);
        }
    }

    /**
     * After a command has been executed, it disables the output.
     */
    public function onTerminate(ConsoleTerminateEvent $event)
    {
        $this->output = null;
    }

    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;

        foreach ($this->sections as $section) {
            if ($output instanceof ConsoleOutput) {
                $section->setOutput($output->section());
            } else {
                $section->setOutput($output);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isHandling(array $record)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(array $record)
    {
        foreach ($this->sections as $section) {
            $section->handle($record);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function handleBatch(array $records)
    {
        foreach ($records as $record) {
            $this->handle($record);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function pushProcessor($callback)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('Processors must be valid callables (callback or object with an __invoke method), '.var_export($callback, true).' given');
        }
        array_unshift($this->processors, $callback);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function popProcessor()
    {
        if (!$this->processors) {
            throw new \LogicException('You tried to pop from an empty processor stack.');
        }

        return array_shift($this->processors);
    }

    /**
     * {@inheritdoc}
     */
    public function setFormatter(FormatterInterface $formatter)
    {
        throw new \LogicException(sprintf("You can't change formatter of %s", __CLASS__));
    }

    /**
     * {@inheritdoc}
     */
    public function getFormatter()
    {
        return $this->_formatter;
    }

    /**
     * @return SectionInterface[]
     */
    public function getSections()
    {
        return $this->sections;
    }

    public function pushSection(SectionInterface $section): void
    {
        $this->sections[] = $section;
        if ($this->output) {
            if ($this->output instanceof ConsoleOutput) {
                $this->setSectionOutput($section, $this->output->section());
            } else {
                $this->setSectionOutput($section, $this->output);
            }
        }
    }

    /**
     * @param SectionInterface[] $sections
     */
    public function setSections(array $sections)
    {
        $this->sections = [];
        foreach ($sections as $section) {
            $this->pushSection($section);
        }
    }

    private function setSectionOutput(SectionInterface $section, OutputInterface $output)
    {
        $section->setOutput($output);
        $this->sectionOutputs[spl_object_hash($section)] = $output;
    }
}
