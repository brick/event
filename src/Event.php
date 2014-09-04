<?php

namespace Brick\Event;

/**
 * Base class that events must extend.
 */
abstract class Event
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
