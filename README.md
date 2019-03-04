# Fratily Event Dispatcher

fratily/event-dispatcher is simple event dispatcher.

## Install

```bash
$ composer require fratily/event-dispatcher
```

## Uses

```php
$provider   = new Fratily\EventDispatcher\ListenerProvider();
$dispatcher = new Fratily\EventDispatcher\EventDispatcher($provider);

$provider->listen(function(SampleEvent $event){/* listener A */}); // Default priority 0
$provider->listen(function(SampleEvent $event){/* listener B */})->setPriority(10);
$provider->listen(function(SampleEvent $event){/* listener C */})->setPriority(20);
$provider->listen(function(SampleEvent $event){/* listener D */})->setPriority(-10);
$provider->listen(function(SampleEvent $event){/* listener E */})->setPriority(-20);
$provider->listen(function(SampleEvent $event){/* listener F */}); // Default priority 0

$event  = new SampleEvent();

$dispatcher->dispatch($event);
```

Dispatch event order:

1. C
1. B
1. A
1. F
1. D
1. E
