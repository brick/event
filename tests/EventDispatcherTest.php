<?php

namespace Brick\Event\Tests;

use Brick\Event\EventDispatcher;

use PHPUnit\Framework\TestCase;

/**
 * Unit tests for class EventDispatcher.
 */
class EventDispatcherTest extends TestCase
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

    /**
     * @param LoggerListener $listener      The logger listener.
     * @param mixed          ...$parameters The expected logged parameters.
     */
    private function assertReceivedParameters(LoggerListener $listener)
    {
        $parameters = array_slice(func_get_args(), 1);
        $this->assertSame($parameters, $listener->getReceivedParameters());
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

        $dispatcher->dispatch('x', '1');

        $this->assertReceivedParameters($listener1, '1');
        $this->assertReceivedParameters($listener2, '1');

        $dispatcher->dispatch('y', '2');

        $this->assertReceivedParameters($listener1, '1');
        $this->assertReceivedParameters($listener2, '1', '2');

        $listener1->setStopPropagation(true);
        $dispatcher->dispatch('x', '3');

        $this->assertReceivedParameters($listener1, '1', '3');
        $this->assertReceivedParameters($listener2, '1', '2');

        $listener1->setStopPropagation(false);
        $listener2->setStopPropagation(true);
        $dispatcher->dispatch('x', '4', '5');

        $this->assertReceivedParameters($listener1, '1', '3', '4', '5');
        $this->assertReceivedParameters($listener2, '1', '2', '4', '5');

        $dispatcher->dispatch('z', '6');

        $this->assertReceivedParameters($listener1, '1', '3', '4', '5');
        $this->assertReceivedParameters($listener2, '1', '2', '4', '5');
    }

    public function testListenerReceivesParameters()
    {
        $event      = 'test';
        $parameters = ['a', 'b', 'c'];
        $dispatcher = new EventDispatcher();

        $dispatcher->addListener($event, function() use ($parameters) {
            $this->assertSame($parameters, func_get_args());
        });

        array_unshift($parameters, $event);
        call_user_func_array([$dispatcher, 'dispatch'], $parameters);
    }
}
