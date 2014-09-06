<?php

namespace Brick\Event;

/**
 * Dispatches events to registered listeners.
 */
final class EventDispatcher
{
    /**
     * The event listeners, indexed by type and priority.
     *
     * @var array
     */
    private $listeners = [];

    /**
     * A cache of the sorted event listeners, indexed by type.
     *
     * @var array
     */
    private $sorted = [];

    /**
     * Adds an event listener.
     *
     * If the listener is already registered for this type, it will be registered again:
     * several instances of a same listener can be registered for a single type.
     *
     * Every listener can stop event propagation by returning `false`.
     *
     * @param string   $type     The event type.
     * @param callable $listener The event listener.
     * @param integer  $priority The higher the priority, the earlier the listener will be called in the chain.
     *
     * @return void
     */
    public function addListener($type, callable $listener, $priority = 0)
    {
        $this->listeners[$type][$priority][] = $listener;
        unset($this->sorted[$type]);
    }

    /**
     * Removes an event listener.
     *
     * If the listener is not registered for this type, this method does nothing.
     * If the listener has been registered several times for this type, all instances are removed.
     *
     * The listener will be called with 3 parameters:
     *
     * - The event : `object`
     * - The event type : `string`
     * - The event dispatcher: `EventDispatcher`
     *
     * @param string   $type     The event type.
     * @param callable $listener The event listener.
     *
     * @return void
     */
    public function removeListener($type, callable $listener)
    {
        if (isset($this->listeners[$type])) {
            foreach ($this->listeners[$type] as $priority => $listeners) {
                foreach ($this->listeners[$type][$priority] as $key => $instance) {
                    if ($instance === $listener) {
                        unset($this->listeners[$type][$priority][$key]);
                        unset($this->sorted[$type]);

                        if (empty($this->listeners[$type][$priority])) {
                            unset($this->listeners[$type][$priority]);

                            if (empty($this->listeners[$type])) {
                                unset($this->listeners[$type]);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Returns all registered listeners for the given type.
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

        if (! isset($this->sorted[$type])) {
            $this->sorted[$type] = $this->sortListeners($this->listeners[$type]);
        }

        return $this->sorted[$type];
    }

    /**
     * Returns all registered listeners indexed by type.
     *
     * Listeners are returned in the order they will be called for each type.
     *
     * @return callable[][]
     */
    public function getAllListeners()
    {
        foreach ($this->listeners as $type => $listeners) {
            if (! isset($this->sorted[$type])) {
                $this->sorted[$type] = $this->sortListeners($listeners);
            }
        }

        return $this->sorted;
    }

    /**
     * Dispatches an event to the registered listeners.
     *
     * The highest priority listeners will be called first.
     * If several listeners have the same priority, they will be called in the order they have been registered.
     *
     * @param string $type  The event type.
     * @param object $event The event to dispatch.
     *
     * @return void
     */
    public function dispatch($type, $event)
    {
        foreach ($this->getListeners($type) as $listener) {
            if ($listener($event, $type, $this) === false) {
                break;
            }
        }
    }

    /**
     * @param string $listenersByPriority
     *
     * @return array
     */
    private function sortListeners($listenersByPriority)
    {
        krsort($listenersByPriority);

        return call_user_func_array('array_merge', $listenersByPriority);
    }
}
