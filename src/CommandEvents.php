<?php
/* 
 * This file is part of chrisguitarguy/tactician-symfony-events
 *
 * For full copyright and license information please see the LICENSE file
 * that was distributed with this source code.
 */

namespace Chrisguitarguy\Tactician\SymfonyEvents;

/**
 * Defines some constants to reference for event names.
 *
 * @since 1.0
 */
final class CommandEvents
{
    public const RECEIVED = 'command.received';
    public const HANDLED = 'command.handled';
    public const FAILED = 'command.failed';

    public static function received($command)
    {
        return self::eventName(self::RECEIVED, $command);
    }

    public static function handled($command)
    {
        return self::eventName(self::HANDLED, $command);
    }

    public static function failed($command)
    {
        return self::eventName(self::FAILED, $command);
    }

    private static function eventName(string $what, $command): string
    {
        return \sprintf('%s.%s', $what, \is_object($command) ? \get_class($command) : (string) $command);
    }

    // @codeCoverageIgnoreStart
    private function __construct()
    {
        // noop
    }
    // @codeCoverageIgnoreEnd
}
