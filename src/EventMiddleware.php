<?php
/* 
 * This file is part of chrisguitarguy/tactician-symfony-events
 *
 * For full copyright and license information please see the LICENSE file
 * that was distributed with this source code.
 */

namespace Chrisguitarguy\Tactician\SymfonyEvents;

use League\Tactician\Middleware;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Sends events when a command is received, handled, or fails.
 *
 * @since 1.0
 */
final class EventMiddleware implements Middleware
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($command, callable $next)
    {
        try {
            $this->dispatch(CommandEvents::RECEIVED, new CommandReceived($command));
            $this->dispatch(CommandEvents::received($command), new CommandReceived($command));

            $result = $next($command);

            $this->dispatch(CommandEvents::HANDLED, new CommandHandled($command, $result));
            $this->dispatch(CommandEvents::handled($command), new CommandHandled($command, $result));

            return $result;
        } catch (\Throwable $e) {
            $this->dispatch(CommandEvents::FAILED, new CommandFailed($command, $e));
            $this->dispatch(CommandEvents::failed($command), new CommandFailed($command, $e));

            throw $e;
        }
    }

    private function dispatch($eventName, CommandEvent $event): void
    {
        $this->dispatcher->dispatch($event, $eventName);
    }
}
