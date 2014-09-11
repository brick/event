###### This development branch uses variadics, and thus restricts compatibility to PHP 5.6

---

Brick\Event
===========

A simple event dispatching mechanism.

[![Build Status](https://secure.travis-ci.org/brick/event.png?branch=PHP-5.6)](http://travis-ci.org/brick/event)
[![Coverage Status](https://coveralls.io/repos/brick/event/badge.png?branch=PHP-5.6)](https://coveralls.io/r/brick/event?branch=PHP-5.6)

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

This library requires PHP 5.6 or [HHVM](http://hhvm.com/) 3.2.

Overview
--------

This package provides the `EventDispatcher`.
The dispatcher dispatches *events*: an event is a unique `string` along with optional parameters.
The events are intercepted by *listeners*: any `callable` can be an event listener.

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

Now, let's dispatch some events:

    $dispatcher->dispatch('startup'); // will display "Caught startup event"
    $dispatcher->dispatch('shutdown'); // will display "Caught shutdown event"

Any additional parameters you pass to `dispatch()` are forwarded to the listeners:

    $dispatcher->addListener('test', function($a, $b) {
        echo "Caught $a and $b";
    });

    $dispatcher->dispatch('test', 'Hello', 'World'); // will display "Caught Hello and World"

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
