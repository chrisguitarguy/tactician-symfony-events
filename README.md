# Tactician Symfony Event Middleware

Use the symfony event dispatcher to get notifications when a command is
received, handle and/or errors.

## Usage

Add the middleware anywhere anywhere before the command handler middleware.

```php
use Symfony\Component\EventDispatcher\EventDispatcher;
use League\Tactician\CommandBus;
use Chrisguitarguy\Tactician\SymfonyEvents\EventMiddleware;
use Chrisguitarguy\Tactician\SymfonyEvents\CommandRecieved;
use Chrisguitarguy\Tactician\SymfonyEvents\CommandHandled;
use Chrisguitarguy\Tactician\SymfonyEvents\CommandFailed;

$eventMiddleware = new EventMiddlware($dispatcher = new EventDispatcher());

$commandBus = new CommandBus([
  // ...
  $eventMiddleware,
  $commandHandlerMiddleware,
  // ...
]);

```

You can listen for events specific to a single command.

```php
use Acme\Example\SomeCommand;

$dispatcher->addListener('command.receieved.'.MyCommand::class, function (CommandReceived $event) {
    // called before the handler runs on SomeCommand
});

$dispatcher->addListener('command.handled.'.MyCommand::class, function (CommandHandled $event) {
    // called after the handler runs on SomeCommand
});

$dispatcher->addListener('command.failed.'.MyCommand::class, function (CommandFailed $event) {
    // called if one the handler or one of the `received` or `handled`
    // events throws an exeception from handling SomeCommand
});

$commandBus->execute(new SomeCommand());
```

Or listen to more generic events that fire on all commands.

```php
use Acme\Example\SomeCommand;

$dispatcher->addListener('command.receieved', function (CommandReceived $event) {
    // called before the handler runs on all commands
});

$dispatcher->addListener('command.handled', function (CommandHandled $event) {
    // called after the handler runs on all commands
});

$dispatcher->addListener('command.failed', function (CommandFailed $event) {
    // called if one the handler or one of the `received` or `handled`
    // events throws an exception from handling any command
});

$commandBus->execute(new SomeCommand());
```

There's also a `CommandEvents` class that provides some constants and helpers
for event names.

```
use Chrisguitarguy\Tactician\SymfonyEvents\CommandEvents;
use Acme\Example\SomeCommand;

// generic events are in constants.
$dispatcher->addListener(CommandEvents::RECEIVED, /*...*/);

// specific have helpers that take an object or class name
$dispatcher->addListener(CommandEvents::received(SomeCommand::class), /*...*/);

$command = new SomeCommand();
$dispatcher->addListener(CommandEvents::received($command), /*...*/);

$dispatcher->addListener(CommandEvents::HANDLED, /*...*/);
$dispatcher->addListener(CommandEvents::handled(SomeCommand::class), /*...*/);

$dispatcher->addListener(CommandEvents::FAILED, /*...*/);
$dispatcher->addListener(CommandEvents::failed(SomeCommand::class), /*...*/);
```

## License

MIT. See the `LICENSE` file.
