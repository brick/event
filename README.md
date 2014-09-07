Brick\Event
===========

A simple event dispatching mechanism.

[![Build Status](https://secure.travis-ci.org/brick/event.png)](http://travis-ci.org/brick/event)
[![Coverage Status](https://coveralls.io/repos/brick/event/badge.png)](https://coveralls.io/r/brick/event)

Introduction
------------

This library helps to write extensible software by plugging in external listeners to events dispatched by an application.

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

This package provides the `EventDispatcher`.
The dispatcher dispatches *events*, that can be any `object`.
The events are intercepted by *listeners*, that can be any `callable`.

### Basic usage

Let's instantiate a dispatcher:

    use Brick\Event\EventDispatcher;
    
    $dispatcher = new EventDispatcher();

And add a few listeners:

    $dispatcher->addListener('startup', function() {
        echo 'Caught startup event';
    });
    
    $dispatcher->addListener('shutdown', function() {
        echo 'Caught shutdown event';
    });

Now, let's dispatch events. Typically, you'll create event classes to store information about what's occurring in your application, but for now, let's just dispatch a simple `StdClass`:

    $dispatcher->dispatch('startup', new \StdClass()); // will display "Caught startup event"
    $dispatcher->dispatch('shutdown', new \StdClass()); // will display "Caught shutdown event"

When an event is dispatched, the dispatcher will look for listeners registered for the given event type, and call each of them with 3 parameters:

- The event : `object`
- The event type : `string`
- The event dispatcher : `EventDispatcher`

If we need this information, we can rewrite our listeners like this:

    function($event, $type, $dispatcher) { ... }

In our example, `$event` would contain the `StdClass` object, and `$type` would contain `'startup'` or `'shutdown'`.

All the listeners registered for the given event type will be invoked, ordered by priority, unless one of the listeners stops the propagation.

### Setting priorities

By default, the listeners are called in the order they have been registered. It is possible to bypass this
natural order by passing a priority to `addListener()`:

    $dispatcher->addListener('startup', function() { ... }, 10);

The default priority is `0`. The listeners with the highest priority will be called first in the chain.
Two listeners with the same priority will be called in the order they have been registered.

### Stopping event propagation

Any listener can decide that the event should not be propagated to further listeners in the chain, by returning `false`:

    $dispatcher->addListener('startup', function() {
        // ...

        return false;
    });

The dispatcher will then break the chain and no further listeners will be called for this event.
