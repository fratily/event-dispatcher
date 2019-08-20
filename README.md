# Fratily Event Dispatcher

fratily/event-dispatcher is simple event dispatcher.

## Install

```bash
$ composer require fratily/event-dispatcher
```

## Usage

```php
$provider   = new Fratily\EventDispatcher\ListenerProvider();
$dispatcher = new Fratily\EventDispatcher\EventDispatcher($provider);

$provider->listen(function(SampleEvent $event){/* A */});
$provider->listen(function(SampleEvent $event){/* B */}, 10);
$provider->listen(function(SampleEvent $event){/* C */}, 20);
$provider->listen(function(SampleEvent $event){/* D */}, -10);
$provider->listen(function(SampleEvent $event){/* E */}, -20);
$provider->listen(function(SampleEvent $event){/* F */});

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
