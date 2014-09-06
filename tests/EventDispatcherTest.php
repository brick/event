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
        foreach ($listeners as $type => $expected) {
            $this->assertSame($expected, $dispatcher->getListeners($type));
        }
    }

    public function testAddRemove()
    {
        $a = function() {};
        $b = function() {};
        $c = function() {};

        $dispatcher = new EventDispatcher();
        $this->assertListenersEqual($dispatcher, [
            'x' => [],
            'y' => [],
            'z' => []
        ]);

        $dispatcher->addListener('x', $a);
        $this->assertListenersEqual($dispatcher, [
            'x' => [$a],
            'y' => [],
            'z' => []
        ]);

        $dispatcher->addListener('x', $b);
        $this->assertListenersEqual($dispatcher, [
            'x' => [$a, $b],
            'y' => [],
            'z' => []
        ]);

        $dispatcher->addListener('y', $a);
        $this->assertListenersEqual($dispatcher, [
            'x' => [$a, $b],
            'y' => [$a],
            'z' => []
        ]);

        $dispatcher->addListener('y', $c, 0);
        $this->assertListenersEqual($dispatcher, [
            'x' => [$a, $b],
            'y' => [$a, $c],
            'z' => []
        ]);

        $dispatcher->addListener('x', $c, -1);
        $this->assertListenersEqual($dispatcher, [
            'x' => [$a, $b, $c],
            'y' => [$a, $c],
            'z' => []
        ]);

        $dispatcher->addListener('x', $b, -1);
        $this->assertListenersEqual($dispatcher, [
            'x' => [$a, $b, $c, $b],
            'y' => [$a, $c],
            'z' => []
        ]);

        $dispatcher->addListener('x', $c, 1);
        $this->assertListenersEqual($dispatcher, [
            'x' => [$c, $a, $b, $c, $b],
            'y' => [$a, $c],
            'z' => []
        ]);

        $dispatcher->addListener('x', $a, 0);
        $this->assertListenersEqual($dispatcher, [
            'x' => [$c, $a, $b, $a, $c, $b],
            'y' => [$a, $c],
            'z' => []
        ]);

        $dispatcher->addListener('y', $b, 1);
        $this->assertListenersEqual($dispatcher, [
            'x' => [$c, $a, $b, $a, $c, $b],
            'y' => [$b, $a, $c],
            'z' => []
        ]);

        $dispatcher->addListener('y', $c, 2);
        $this->assertListenersEqual($dispatcher, [
            'x' => [$c, $a, $b, $a, $c, $b],
            'y' => [$c, $b, $a, $c],
            'z' => []
        ]);

        $dispatcher->addListener('y', $a, 1);
        $this->assertListenersEqual($dispatcher, [
            'x' => [$c, $a, $b, $a, $c, $b],
            'y' => [$c, $b, $a, $a, $c],
            'z' => []
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
    }

    public function testDispatch()
    {
        $dispatcher = new EventDispatcher();

        $listener1 = new LoggerListener();
        $listener2 = new LoggerListener();

        $dispatcher->addListener('x', $listener1);
        $dispatcher->addListener('x', $listener2);

        $event1 = new \StdClass();
        $event2 = new \StdClass();
        $event3 = new \StdClass();

        $dispatcher->dispatch('x', $event1);

        $this->assertSame([$event1], $listener1->getReceivedEvents());
        $this->assertSame([$event1], $listener2->getReceivedEvents());

        $listener1->setStopPropagation(true);

        $dispatcher->dispatch('x', $event2);

        $this->assertSame([$event1, $event2], $listener1->getReceivedEvents());
        $this->assertSame([$event1], $listener2->getReceivedEvents());

        $listener1->setStopPropagation(false);
        $listener2->setStopPropagation(true);
        $dispatcher->dispatch('x', $event3);

        $this->assertSame([$event1, $event2, $event3], $listener1->getReceivedEvents());
        $this->assertSame([$event1, $event3], $listener2->getReceivedEvents());
    }
}
