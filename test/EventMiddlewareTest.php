<?php
/* 
 * This file is part of chrisguitarguy/tactician-symfony-events
 *
 * For full copyright and license information please see the LICENSE file
 * that was distributed with this source code.
 */

namespace Chrisguitarguy\Tactician\SymfonyEvents\Tests;

use Chrisguitarguy\Tactician\SymfonyEvents\CommandEvents;
use Chrisguitarguy\Tactician\SymfonyEvents\EventMiddleware;
use League\Tactician\CommandBus;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\Locator\InMemoryLocator;
use League\Tactician\Handler\MethodNameInflector\HandleInflector;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

final class Command
{

}

// so we can mock this below and change behavior
interface Handler
{
    public function handle($command);
}

/**
 * An integration test, since this library does absolutely nothing on its own.
 *
 * @since 1.0
 */
final class EventMiddlewareTest extends TestCase
{
    private $dispatcher, $bus, $handler;

    protected function setUp(): void
    {
        $this->dispatcher = new EventDispatcher();
        $this->handler = $this->createMock(Handler::class);
        $this->bus = new CommandBus([
            new EventMiddleware($this->dispatcher),
            new CommandHandlerMiddleware(
                new ClassNameExtractor(),
                new InMemoryLocator([Command::class => $this->handler]),
                new HandleInflector()
            ),
        ]);
    }

    public function testEventMiddlewareFiresGenericReceievedEvent(): void
    {
        $eventCommand = null;
        $this->dispatcher->addListener(CommandEvents::RECEIVED, static function ($e) use (&$eventCommand) {
            $eventCommand = $e->getCommand();
        });

        $this->bus->handle($command = new Command());

        $this->assertSame($command, $eventCommand);
    }

    public function testEventMiddlewareFiresTheCommandSpecificReceivedEvent(): void
    {
        $eventCommand = null;
        $this->dispatcher->addListener(CommandEvents::received(Command::class), static function ($e) use (&$eventCommand) {
            $eventCommand = $e->getCommand();
        });

        $this->bus->handle($command = new Command());

        $this->assertSame($command, $eventCommand);
    }

    public function testEventMiddlwareFiresTheGenericHandledEventWithTheCommandAndResult(): void
    {
        $eventCommand = $eventResult = null;
        $this->dispatcher->addListener(CommandEvents::HANDLED, static function ($e) use (&$eventCommand, &$eventResult) {
            $eventCommand = $e->getCommand();
            $eventResult = $e->getResult();
        });
        $this->handlerWill($this->returnValue($result = new \stdClass));

        $this->bus->handle($command = new Command());

        $this->assertSame($command, $eventCommand);
        $this->assertSame($result, $eventResult);
    }

    public function testEventMiddlwareFiresTheSpecificHandledEventWithTheCommandAndResult(): void
    {
        $eventCommand = $eventResult = null;
        $this->handlerWill($this->returnValue($result = new \stdClass));
        $this->dispatcher->addListener(CommandEvents::handled(Command::class), static function ($e) use (&$eventCommand, &$eventResult) {
            $eventCommand = $e->getCommand();
            $eventResult = $e->getResult();
        });

        $this->bus->handle($command = new Command());

        $this->assertSame($command, $eventCommand);
        $this->assertSame($result, $eventResult);
    }

    public function testEventMiddlewareFiresTheGenericFailedEventWithTheException(): void
    {
        $eventCommand = null;
        $this->handlerWill($this->throwException($ex = new \LogicException('oops')));
        $this->dispatcher->addListener(CommandEvents::FAILED, function ($e) use (&$eventCommand, $ex) {
            $this->assertSame($ex, $e->getException());
            $eventCommand = $e->getCommand();
        });

        // no exepcted exception here because we want to make sure 
        // our listener is actually called
        $caught = false;
        try {
            $this->bus->handle($command = new Command());
        } catch (\LogicException $e) {
            $this->assertSame($ex, $e);
            $caught = true;
        }

        $this->assertSame($command, $eventCommand);
        $this->assertTrue($caught, 'should have caught the LogicException above');
    }

    public function testEventMiddlewareFiresTheSpecificFailedEventWithTheException(): void
    {
        $eventCommand = null;
        $this->handlerWill($this->throwException($ex = new \LogicException('oops')));
        $this->dispatcher->addListener(CommandEvents::failed(Command::class), function ($e) use (&$eventCommand, $ex) {
            $this->assertSame($ex, $e->getException());
            $eventCommand = $e->getCommand();
        });

        // no exepcted exception here because we want to make sure 
        // our listener is actually called
        $caught = false;
        try {
            $this->bus->handle($command = new Command());
        } catch (\LogicException $e) {
            $this->assertSame($ex, $e);
            $caught = true;
        }

        $this->assertSame($command, $eventCommand);
        $this->assertTrue($caught, 'should have caught the LogicException above');
    }

    private function handlerWill($action): void
    {
        $this->handler->expects($this->once())
            ->method('handle')
            ->will($action);
    }
}
