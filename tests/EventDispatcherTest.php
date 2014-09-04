<?php

namespace Brick\Event\Tests;

use Brick\Event\EventDispatcher;
use Brick\Event\Tests\Objects\BasicEvent;
use Brick\Event\Tests\Objects\LoggerListener;

/**
 * Unit tests for class EventDispatcher.
 */
class EventDispatcherTest extends \PHPUnit_Framework_TestCase
{
    public function testAddRemove()
    {
        $a = new LoggerListener();
        $b = new LoggerListener();
        $c = new LoggerListener();

        $dispatcher = new EventDispatcher();
        $this->assertSame([], $dispatcher->getListeners());

        $dispatcher->addListener($a);
        $this->assertSame([$a], $dispatcher->getListeners());

        $dispatcher->addListener($b);
        $this->assertSame([$a, $b], $dispatcher->getListeners());

        $dispatcher->addListener($a);
        $this->assertSame([$b, $a], $dispatcher->getListeners());

        $dispatcher->addListener($c, 1);
        $this->assertSame([$c, $b, $a], $dispatcher->getListeners());

        $dispatcher->addListener($c, -1);
        $this->assertSame([$b, $a, $c], $dispatcher->getListeners());

        $dispatcher->addListener($a, -1);
        $this->assertSame([$b, $c, $a], $dispatcher->getListeners());

        $dispatcher->addListener($b, -2);
        $this->assertSame([$c, $a, $b], $dispatcher->getListeners());

        $dispatcher->addListener($a, -2);
        $this->assertSame([$c, $b, $a], $dispatcher->getListeners());

        $dispatcher->addListener($c, -2);
        $this->assertSame([$b, $a, $c], $dispatcher->getListeners());

        $dispatcher->addListener($a, -2);
        $this->assertSame([$b, $c, $a], $dispatcher->getListeners());

        $dispatcher->removeListener($a);
        $this->assertSame([$b, $c], $dispatcher->getListeners());

        $dispatcher->removeListener($a);
        $this->assertSame([$b, $c], $dispatcher->getListeners());

        $dispatcher->removeListener($b);
        $this->assertSame([$c], $dispatcher->getListeners());

        $dispatcher->removeListener($c);
        $this->assertSame([], $dispatcher->getListeners());
    }

    public function testDispatch()
    {
        $dispatcher = new EventDispatcher();

        $listener1 = new LoggerListener();
        $listener2 = new LoggerListener();

        $dispatcher->addListener($listener1);
        $dispatcher->addListener($listener2);

        $event1 = new BasicEvent();
        $event2 = new BasicEvent();
        $event3 = new BasicEvent();

        $dispatcher->dispatch($event1);

        $this->assertSame([$event1], $listener1->getReceivedEvents());
        $this->assertSame([$event1], $listener2->getReceivedEvents());

        $listener1->setStopPropagation(true);

        $dispatcher->dispatch($event2);

        $this->assertSame([$event1, $event2], $listener1->getReceivedEvents());
        $this->assertSame([$event1], $listener2->getReceivedEvents());

        $event3->stopPropagation();
        $dispatcher->dispatch($event3);

        $this->assertSame([$event1, $event2], $listener1->getReceivedEvents());
        $this->assertSame([$event1], $listener2->getReceivedEvents());
    }
}
