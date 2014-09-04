<?php

namespace Brick\Event\Test;

use Brick\Event\Tests\Objects\BasicEvent;

/**
 * Unit tests for class Event.
 */
class EventTest extends \PHPUnit_Framework_TestCase
{
    public function testStopPropagation()
    {
        $event = new BasicEvent();
        $this->assertFalse($event->isPropagationStopped());

        $event->stopPropagation();
        $this->assertTrue($event->isPropagationStopped());
    }
}
