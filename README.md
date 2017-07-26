# Wwwision.EventScheduler

Simple Event scheduler for the [Neos.EventSourcing](https://github.com/neos/Neos.EventSourcing) Flow package

## Usage

```php
class SomeClass implements EventListenerInterface
{
    const TASK_TYPE = 'plans';

    /**
     * @Flow\Inject
     * @var EventScheduler
     */
    protected $scheduler;

    public function whenSomeThingWasPlanned(SomeThingWasPlanned $event): void
    {
        $taskPayload = ['nameOfTheThing' => $event->getTitle()];
        $this->scheduler->scheduleTask($event->getCorrelationId(), self::TASK_TYPE, $event->getPlannedDate(), $taskPayload);
    }

    public function whenSomeThingWasCancelled(SomeThingWasCancelled $event): void
    {
        $this->scheduler->cancelTask($event->getCorrelationId(), self::TASK_TYPE);
    }

    public function whenSchedulerWasTriggered(SchedulerWasTriggered $event): void
    {
        if (!$event->matchesType(self::TASK_TYPE)) {
            return;
        }
        $payload = $event->getPayload();

        // TODO Do something with the $paload
    }
}
```

To make this work the `./flow wwwision.eventscheduler:scheduler:run` command needs to be executed regularly (e.g. via cron)

*Note:* In this example `SomeThingWasPlanned` and `SomeThingWasCancelled` are domain events.
`SchedulerWasTriggered` is an "integration event" that triggered by the `EventScheduler` as soon as the given timestamp is reached.