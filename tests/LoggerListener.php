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
     * @var boolean
     */
    private $stopPropagation = false;

    /**
     * @param mixed ...$parameters
     *
     * @return false|null
     */
    public function __invoke(...$parameters)
    {
        $this->receivedParameters = array_merge($this->receivedParameters, $parameters);

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
     * @param boolean $stopPropagation
     *
     * @return void
     */
    public function setStopPropagation($stopPropagation)
    {
        $this->stopPropagation = $stopPropagation;
    }
}
