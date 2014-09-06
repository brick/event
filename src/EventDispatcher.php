<?php

namespace Brick\Event;

/**
 * Dispatches events to registered listeners.
 */
final class EventDispatcher
{
    /**
     * The event listeners, indexed by type.
     *
     * @var array<string, array<integer, callable>>
     */
    private $listeners = [];

    /**
     * Adds an event listener.
     *
     * If the listener is already registered for this type, it will be registered again.
     * Several instances of a same listener can be registered for a single type.
     *
     * @param string   $type     The event type.
     * @param callable $listener The event listener.
     * @param integer  $priority The higher the priority, the earlier the listener will be called in the chain.
     *
     * @return EventDispatcher This instance, for chaining.
     */
    public function addListener($type, callable $listener, $priority = 0)
    {
        $this->listeners[$type][] = [$listener, $priority];

        return $this;
    }

    /**
     * Removes an event listener.
     *
     * If the listener is not registered for this type, this method does nothing.
     * If the listener has been registered several times for this type, all instances are removed.
     *
     * @param string   $type     The event type.
     * @param callable $listener The event listener.
     *
     * @return EventDispatcher This instance, for chaining.
     */
    public function removeListener($type, callable $listener)
    {
        if (isset($this->listeners[$type])) {
            foreach ($this->listeners[$type] as $key => $value) {
                if ($value[0] === $listener) {
                    unset($this->listeners[$type][$key]);
                }
            }
        }

        return $this;
    }

    /**
     * Returns all registered listeners.
     *
     * Listeners are returned in the order they will be called.
     *
     * @param string $type The event type.
     *
     * @return callable[]
     */
    public function getListeners($type)
    {
        if (empty($this->listeners[$type])) {
            return [];
        }

        $listeners = [];
        $index = $count = count($this->listeners[$type]);

        foreach ($this->listeners[$type] as list($listener, $priority)) {
            $listeners[$priority * $count + --$index] = $listener;
        }

        krsort($listeners);

        return array_values($listeners);
    }

    /**
     * Dispatches an event to the registered listeners.
     *
     * The highest priority listeners will be called first.
     * If several listeners have the same priority, they will be called in the order they have been registered.
     *
     * @param string     $type  The event type.
     * @param Event|null $event The event to dispatch, or null to create an empty event.
     *
     * @return Event The dispatched event.
     */
    public function dispatch($type, Event $event = null)
    {
        if ($event === null) {
            $event = new Event();
        }

        foreach ($this->getListeners($type) as $listener) {
            if ($event->isPropagationStopped()) {
                break;
            }

            $listener($event);
        }

        return $event;
    }
}
