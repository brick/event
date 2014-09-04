<?php

namespace Brick\Event;

/**
 * Dispatches events to registered listeners.
 */
final class EventDispatcher
{
    /**
     * @var EventListener[]
     */
    private $listeners = [];

    /**
     * @var array
     */
    private $priorities = [];

    /**
     * Adds an event listener.
     *
     * If the listener is already registered, this method just updates its priority.
     *
     * @param EventListener $listener The event listener.
     * @param integer       $priority The higher the priority, the earlier the listener will be called in the chain.
     *
     * @return EventDispatcher This instance, for chaining.
     */
    public function addListener(EventListener $listener, $priority = 0)
    {
        $hash = spl_object_hash($listener);

        if (isset($this->listeners[$hash])) {
            // Remove the listener first to ensure it will be put back to the top of the stack.
            unset($this->listeners[$hash]);
        }

        $this->listeners[$hash] = $listener;
        $this->priorities[$hash] = $priority;

        return $this;
    }

    /**
     * Removes an event listener.
     *
     * If the listener is not registered, this method does nothing.
     *
     * @param EventListener $listener The event listener.
     *
     * @return EventDispatcher This instance, for chaining.
     */
    public function removeListener(EventListener $listener)
    {
        $hash = spl_object_hash($listener);

        unset($this->listeners[$hash]);
        unset($this->priorities[$hash]);

        return $this;
    }

    /**
     * Dispatches an event to the registered listeners.
     *
     * The highest priority listeners will be called first.
     * If two listeners have the same priority, the first registered will be called first.
     *
     * @param Event $event The event to dispatch.
     *
     * @return EventDispatcher This instance, for chaining.
     */
    public function dispatch(Event $event)
    {
        $listeners = [];
        $index = $count = count($this->listeners);

        foreach ($this->listeners as $hash => $listener) {
            $priority = $this->priorities[$hash];
            $listeners[$priority * $count + --$index] = $listener;
        }

        krsort($listeners);

        foreach ($listeners as $listener) {
            if ($event->isPropagationStopped()) {
                break;
            }

            /** @var EventListener $listener */
            $listener->handleEvent($event);
        }

        return $this;
    }
}
