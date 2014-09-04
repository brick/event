<?php

namespace Brick\Event;

/**
 * Interface that event listeners must implement.
 */
interface EventListener
{
    /**
     * @param Event $event
     *
     * @return void
     */
    public function handleEvent(Event $event);
}
