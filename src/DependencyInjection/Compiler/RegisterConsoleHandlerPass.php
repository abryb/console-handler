<?php

namespace Abryb\ConsoleHandler\DependencyInjection;

use Abryb\ConsoleHandler\ConsoleHandler;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Definition;
use Monolog\Logger;

/**
 * @author Błażej Rybarkiewicz <b.rybarkiewicz@gmail.com>
 */
class RegisterConsoleHandlerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        // TODO: Implement process() method.
        if ($container->hasDefinition('monolog.handler.console')) {
            $definition = $container->getDefinition('monolog.handler.console');
            $definition->setClass(ConsoleHandler::class);
        }
    }
}
