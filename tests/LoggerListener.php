<?php

namespace Brick\Event\Tests;

/**
 * Listener that logs all received events for testing purposes.
 */
class LoggerListener
{
    /**
     * All the events this listener has received.
     *
     * @var object[]
     */
    private $receivedEvents = [];

    /**
     * Whether this listener should stop event propagation after recording the event.
     *
     * @var boolean
     */
    private $stopPropagation = false;

    /**
     * @param object $event
     *
     * @return false|null
     */
    public function __invoke($event)
    {
        $this->receivedEvents[] = $event;

        if ($this->stopPropagation) {
            return false;
        }

        return null;
    }

    /**
     * @return object[]
     */
    public function getReceivedEvents()
    {
        return $this->receivedEvents;
    }

    /**
     * @param boolean $stopPropagation
     *
     * @return void
     */
    public function setStopPropagation($stopPropagation)
    {
        $this->stopPropagation = $stopPropagation;
    }
}
