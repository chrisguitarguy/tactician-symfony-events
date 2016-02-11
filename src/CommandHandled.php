<?php
/* 
 * This file is part of chrisguitarguy/tactician-symfony-events
 *
 * For full copyright and license information please see the LICENSE file
 * that was distributed with this source code.
 */

namespace Chrisguitarguy\Tactician\SymfonyEvents;

/**
 * Event object fired after a command is handled.
 *
 * @since 1.0
 */
final class CommandHandled extends CommandEvent
{
    /**
     * @var mixed
     */
    private $result;

    public function __construct($command, $result)
    {
        parent::__construct($command);
        $this->result = $result;
    }

    public function getResult()
    {
        return $this->result;
    }
}
