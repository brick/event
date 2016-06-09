<?php

namespace Brick\Event\Tests;

/**
 * Listener that logs all received events for testing purposes.
 */
class LoggerListener
{
    /**
     * All the event parameters this listener has received.
     *
     * @var array
     */
    private $receivedParameters = [];

    /**
     * Whether this listener should stop event propagation after recording the event.
     *
     * @var bool
     */
    private $stopPropagation = false;

    /**
     * @return false|null
     */
    public function __invoke()
    {
        $this->receivedParameters = array_merge($this->receivedParameters, func_get_args());

        if ($this->stopPropagation) {
            return false;
        }

        return null;
    }

    /**
     * @return array
     */
    public function getReceivedParameters()
    {
        return $this->receivedParameters;
    }

    /**
     * @param bool $stopPropagation
     *
     * @return void
     */
    public function setStopPropagation(bool $stopPropagation)
    {
        $this->stopPropagation = $stopPropagation;
    }
}
