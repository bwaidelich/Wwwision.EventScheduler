<?php
namespace Wwwision\EventScheduler\EventStore;

use Neos\Error\Messages\Result;
use Neos\EventSourcing\EventStore\EventStream;
use Neos\EventSourcing\EventStore\EventStreamFilterInterface;
use Neos\EventSourcing\EventStore\ExpectedVersion;
use Neos\EventSourcing\EventStore\RawEvent;
use Neos\EventSourcing\EventStore\Storage\EventStorageInterface;
use Neos\EventSourcing\EventStore\WritableEvents;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Utility\Now;

class TransientEventStorage implements EventStorageInterface
{

    /**
     * @Flow\Inject(lazy=false)
     * @var Now
     */
    protected $now;

    /**
     * @param EventStreamFilterInterface $filter
     * @return EventStream
     */
    public function load(EventStreamFilterInterface $filter): EventStream
    {
        return new EventStream(new \ArrayIterator());
    }

    /**
     * @param string $streamName
     * @param WritableEvents $events
     * @param int $expectedVersion
     * @return RawEvent[]
     */
    public function commit(string $streamName, WritableEvents $events, int $expectedVersion = ExpectedVersion::ANY): array
    {
        $rawEvents = [];
        foreach ($events as $event) {
            $rawEvents[] = new RawEvent(0, $event->getType(), $event->getData(), $event->getMetadata(), 0, $event->getIdentifier(), $this->now);
        }
        return $rawEvents;
    }

    /**
     * Retrieves the (connection) status of the storage adapter
     *
     * If the result contains no errors, the status is considered valid
     * The result may contain Notices, Warnings and Errors
     *
     * @return Result
     */
    public function getStatus()
    {
        return new Result();
    }

    /**
     * Sets up the configured storage adapter (i.e. creates required database tables) and validates the configuration
     *
     * If the result contains no errors, the setup is considered successful
     * The result may contain Notices, Warnings and Errors
     *
     * @return Result
     */
    public function setup()
    {
        return new Result();
    }
}