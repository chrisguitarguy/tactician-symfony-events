<?php
/* 
 * This file is part of chrisguitarguy/tactician-symfony-events
 *
 * For full copyright and license information please see the LICENSE file
 * that was distributed with this source code.
 */

namespace Chrisguitarguy\Tactician\SymfonyEvents;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * ABC for command events. Provides the getter for the command.
 *
 * @since 1.0
 */
abstract class CommandEvent extends Event
{
    /**
     * @var object
     */
    private $command;

    public function __construct($command)
    {
        $this->command = $command;
    }

    public function getCommand()
    {
        return $this->command;
    }
}
