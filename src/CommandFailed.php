<?php
/* 
 * This file is part of chrisguitarguy/tactician-symfony-events
 *
 * For full copyright and license information please see the LICENSE file
 * that was distributed with this source code.
 */

namespace Chrisguitarguy\Tactician\SymfonyEvents;

/**
 * Event object fired if a command or its events throw an exception.
 *
 * @since 1.0
 */
final class CommandFailed extends CommandEvent
{
    /**
     * @var \Throwable
     */
    private $exception;

    public function __construct($command, \Throwable $exception)
    {
        parent::__construct($command);
        $this->exception = $exception;
    }

    public function getException(): \Throwable
    {
        return $this->exception;
    }
}
