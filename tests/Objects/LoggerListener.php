<?php

namespace Brick\Event\Tests\Objects;

use Brick\Event\Event;
use Brick\Event\EventListener;

/**
 * Listener that logs all received events.
 */
class LoggerListener implements EventListener
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
     * {@inheritdoc}
     */
    public function handleEvent(Event $event)
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
