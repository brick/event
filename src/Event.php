<?php

namespace Brick\Event;

/**
 * An event propagated through the listeners by the dispatcher.
 *
 * This class can be extended.
 */
class Event
{
    /**
     * @var boolean
     */
    private $propagationStopped = false;

    /**
     * @return boolean
     */
    final public function isPropagationStopped()
    {
        return $this->propagationStopped;
    }

    /**
     * @return void
     */
    final public function stopPropagation()
    {
        $this->propagationStopped = true;
    }
}
