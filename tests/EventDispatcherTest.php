<?php

namespace Brick\Event\Tests;

use Brick\Event\EventDispatcher;
use Brick\Event\Tests\Objects\LoggerListener;

/**
 * Unit tests for class EventDispatcher.
 */
class EventDispatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param EventDispatcher $dispatcher
     * @param array           $listeners
     */
    private function assertListenersEqual(EventDispatcher $dispatcher, array $listeners)
    {
        $allListeners = $dispatcher->getAllListeners();

        $this->assertInternalType('array', $allListeners);
        $this->assertCount(count($listeners), $allListeners);

        foreach ($listeners as $type => $expected) {
            $this->assertArrayHasKey($type, $allListeners);
            $this->assertSame($expected, $allListeners[$type]);
            $this->assertSame($expected, $dispatcher->getListeners($type));
        }
    }

    public function testAddRemoveListener()
    {
        $a = function() {};
        $b = function() {};
        $c = function() {};

        $dispatcher = new EventDispatcher();
        $this->assertListenersEqual($dispatcher, []);

        $dispatcher->addListener('x', $a);
        $this->assertListenersEqual($dispatcher, [
            'x' => [$a]
        ]);

        $dispatcher->addListener('x', $b);
        $this->assertListenersEqual($dispatcher, [
            'x' => [$a, $b]
        ]);

        $dispatcher->addListener('y', $a);
        $this->assertListenersEqual($dispatcher, [
            'x' => [$a, $b],
            'y' => [$a]
        ]);

        $dispatcher->addListener('y', $c, 0);
        $this->assertListenersEqual($dispatcher, [
            'x' => [$a, $b],
            'y' => [$a, $c]
        ]);

        $dispatcher->addListener('x', $c, -1);
        $this->assertListenersEqual($dispatcher, [
            'x' => [$a, $b, $c],
            'y' => [$a, $c]
        ]);

        $dispatcher->addListener('x', $b, -1);
        $this->assertListenersEqual($dispatcher, [
            'x' => [$a, $b, $c, $b],
            'y' => [$a, $c]
        ]);

        $dispatcher->addListener('x', $c, 1);
        $this->assertListenersEqual($dispatcher, [
            'x' => [$c, $a, $b, $c, $b],
            'y' => [$a, $c]
        ]);

        $dispatcher->addListener('x', $a, 0);
        $this->assertListenersEqual($dispatcher, [
            'x' => [$c, $a, $b, $a, $c, $b],
            'y' => [$a, $c]
        ]);

        $dispatcher->addListener('y', $b, 1);
        $this->assertListenersEqual($dispatcher, [
            'x' => [$c, $a, $b, $a, $c, $b],
            'y' => [$b, $a, $c]
        ]);

        $dispatcher->addListener('y', $c, 2);
        $this->assertListenersEqual($dispatcher, [
            'x' => [$c, $a, $b, $a, $c, $b],
            'y' => [$c, $b, $a, $c]
        ]);

        $dispatcher->addListener('y', $a, 1);
        $this->assertListenersEqual($dispatcher, [
            'x' => [$c, $a, $b, $a, $c, $b],
            'y' => [$c, $b, $a, $a, $c]
        ]);

        $dispatcher->addListener('z', $a, 1);
        $this->assertListenersEqual($dispatcher, [
            'x' => [$c, $a, $b, $a, $c, $b],
            'y' => [$c, $b, $a, $a, $c],
            'z' => [$a]
        ]);

        $dispatcher->addListener('z', $b, -2);
        $this->assertListenersEqual($dispatcher, [
            'x' => [$c, $a, $b, $a, $c, $b],
            'y' => [$c, $b, $a, $a, $c],
            'z' => [$a, $b]
        ]);

        $dispatcher->addListener('z', $c, -1);
        $this->assertListenersEqual($dispatcher, [
            'x' => [$c, $a, $b, $a, $c, $b],
            'y' => [$c, $b, $a, $a, $c],
            'z' => [$a, $c, $b]
        ]);

        $dispatcher->addListener('z', $c, -1);
        $this->assertListenersEqual($dispatcher, [
            'x' => [$c, $a, $b, $a, $c, $b],
            'y' => [$c, $b, $a, $a, $c],
            'z' => [$a, $c, $c, $b]
        ]);

        $dispatcher->removeListener('z', $c);
        $this->assertListenersEqual($dispatcher, [
            'x' => [$c, $a, $b, $a, $c, $b],
            'y' => [$c, $b, $a, $a, $c],
            'z' => [$a, $b]
        ]);

        $dispatcher->removeListener('y', $b);
        $this->assertListenersEqual($dispatcher, [
            'x' => [$c, $a, $b, $a, $c, $b],
            'y' => [$c, $a, $a, $c],
            'z' => [$a, $b]
        ]);

        $dispatcher->removeListener('x', $a);
        $this->assertListenersEqual($dispatcher, [
            'x' => [$c, $b, $c, $b],
            'y' => [$c, $a, $a, $c],
            'z' => [$a, $b]
        ]);

        $dispatcher->removeListener('z', $a);
        $this->assertListenersEqual($dispatcher, [
            'x' => [$c, $b, $c, $b],
            'y' => [$c, $a, $a, $c],
            'z' => [$b]
        ]);

        $dispatcher->removeListener('y', $c);
        $this->assertListenersEqual($dispatcher, [
            'x' => [$c, $b, $c, $b],
            'y' => [$a, $a],
            'z' => [$b]
        ]);

        $dispatcher->removeListener('x', $b);
        $this->assertListenersEqual($dispatcher, [
            'x' => [$c, $c],
            'y' => [$a, $a],
            'z' => [$b]
        ]);

        $dispatcher->removeListener('z', $b);
        $this->assertListenersEqual($dispatcher, [
            'x' => [$c, $c],
            'y' => [$a, $a]
        ]);

        $dispatcher->removeListener('y', $a);
        $this->assertListenersEqual($dispatcher, [
            'x' => [$c, $c]
        ]);

        $dispatcher->removeListener('x', $c);
        $this->assertListenersEqual($dispatcher, []);
    }

    public function testDispatchAndStopPropagation()
    {
        $dispatcher = new EventDispatcher();

        $listener1 = new LoggerListener();
        $listener2 = new LoggerListener();

        $dispatcher->addListener('x', $listener1);
        $dispatcher->addListener('x', $listener2);
        $dispatcher->addListener('y', $listener2);

        $event1 = new \StdClass();
        $event2 = new \StdClass();
        $event3 = new \StdClass();

        $dispatcher->dispatch('x', $event1);

        $this->assertSame([$event1], $listener1->getReceivedEvents());
        $this->assertSame([$event1], $listener2->getReceivedEvents());

        $dispatcher->dispatch('y', $event2);
        $this->assertSame([$event1], $listener1->getReceivedEvents());
        $this->assertSame([$event1, $event2], $listener2->getReceivedEvents());

        $listener1->setStopPropagation(true);
        $dispatcher->dispatch('x', $event2);

        $this->assertSame([$event1, $event2], $listener1->getReceivedEvents());
        $this->assertSame([$event1, $event2], $listener2->getReceivedEvents());

        $listener1->setStopPropagation(false);
        $listener2->setStopPropagation(true);
        $dispatcher->dispatch('x', $event3);

        $this->assertSame([$event1, $event2, $event3], $listener1->getReceivedEvents());
        $this->assertSame([$event1, $event2, $event3], $listener2->getReceivedEvents());
    }

    public function testListenerReceivesParameters()
    {
        $type       = 'x';
        $event      = new \StdClass();
        $dispatcher = new EventDispatcher();

        $dispatcher->addListener($type, function($e, $t, $d) use ($event, $type, $dispatcher) {
            $this->assertSame($event, $e);
            $this->assertSame($type, $t);
            $this->assertSame($dispatcher, $d);
        });

        $dispatcher->dispatch($type, $event);
    }
}
