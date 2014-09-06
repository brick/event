<?php

namespace Brick\Event\Tests\Objects;

use Brick\Event\Event;

/**
 * Listener that logs all received events for testing purposes.
 */
class LoggerListener
{
    /**
     * All the events this listener has received.
     *
     * @var Event[]
     */
    private $receivedEvents = [];

    /**
     * Whether this listener should stop event propagation after recording the event.
     *
     * @var boolean
     */
    private $stopPropagation = false;

    /**
     * @param Event $event
     */
    public function __invoke(Event $event)
    {
        $this->receivedEvents[] = $event;

        if ($this->stopPropagation) {
            $event->stopPropagation();
        }
    }

    /**
     * @return Event[]
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
