<?php
namespace Wwwision\EventScheduler\Event;

use Neos\EventSourcing\Event\EventInterface;

final class SchedulerWasTriggered implements EventInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $type;

    /**
     * HACK This must not be an \DateTimeInterface because the default DateTimeConverter objects those in canConvertFrom()
     * @var \DateTimeImmutable
     */
    private $timestamp;

    /**
     * @var array
     */
    private $payload;

    /**
     * @param string $id Type of the task (unique per $type)
     * @param string $type Arbitrary type this task is categorized with
     * @param \DateTimeImmutable $timestamp When was the task originally scheduled for
     * @param array $payload Optional task payload
     */
    public function __construct(string $id, string $type, \DateTimeImmutable $timestamp, array $payload = null)
    {
        $this->id = $id;
        $this->type = $type;
        $this->timestamp = $timestamp;
        $this->payload = $payload;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTimestamp(): \DateTimeImmutable
    {
        return $this->timestamp;
    }

    public function getPayload(): ?array
    {
        return $this->payload;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function matchesType(string $type = null): bool
    {
        return $this->type === $type;
    }

}