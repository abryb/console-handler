<?php


namespace Abryb\ConsoleHandler;


/**
 * @author Błażej Rybarkiewicz <b.rybarkiewicz@gmail.com>
 */
interface ConsoleHandlerAwareCommand
{
    public function configureConsoleHandler(ConsoleHandler $consoleHandler);
}
