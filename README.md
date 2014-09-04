Brick\Event
===========

A simple event dispatching and listening mechanism.

[![Build Status](https://secure.travis-ci.org/brick/event.png)](http://travis-ci.org/brick/event)
[![Coverage Status](https://coveralls.io/repos/brick/event/badge.png)](https://coveralls.io/r/brick/event)

Introduction
------------

This library helps to write extensible software by plugging in external objects that will
listen to events dispatched by your application.

Installation
------------

This library is installable via [Composer](https://getcomposer.org/).
Just define the following requirement in your `composer.json` file:

    {
        "require": {
            "brick/event": "dev-master"
        }
    }

Requirements
------------

This library requires PHP 5.4 or higher. [HHVM](http://hhvm.com/) is officially supported.

Overview
--------

### Classes

There are three classes in the package:

- `Event`: base class that events must extend
- `EventListener`: interface that event listeners must implement
- `EventDispatcher`: registers the listeners and dispatches the events

These classes belong to the `Brick\Event` namespace.

### Basic usage

Let's create a couple of events:

    use Brick\Event\Event;

    class StartupEvent extends Event
    {
    }

    class ShutdownEvent extends Event
    {
    }

And a listener for these events:

    use Brick\Event\Event;
    use Brick\Event\EventListener;

    class StartupShutdownListener implements EventListener
    {
        public function handleEvent(Event $event)
        {
            if ($event instanceof StartupEvent) {
                echo 'Caught startup event';
            }
            if ($event instanceof ShutdownEvent) {
                echo 'Caught shutdown event';
            }
        }
    }

Now, let's instantiate a dispatcher and run it:

    use Brick\Event\EventDispatcher;
    
    $dispatcher = new EventDispatcher();
    $dispatcher->addListener(new StartupShutdownListener());
    
    $dispatcher->dispatch(new StartupEvent()); // will display "Caught startup event"
    $dispatcher->dispatch(new ShutdownEvent()); // will display "Caught shutdown event"

When an event is dispatched, all listeners are called without distinction. Each listener must check if the event
is relevant to it using `instanceof`.

### Setting priorities

By default, the listeners are called in the order they have been registered. It is possible to bypass this
natural order by passing a priority to `addListener()`:

    $dispatcher->addListener(new StartupShutdownListener(), 10);

The default priority is `0`. The listeners with the highest priority will be called first in the chain.
Two listeners with the same priority will be called in the order they have been registered.

### Stopping event propagation

Any listener can decide that the event should not be propagated to further listeners:

    public function handleEvent(Event $event)
    {
        // ...

        $event->stopPropagation();
    }

The dispatcher will then break the chain and no further listeners will be called for this event.
