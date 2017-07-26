<?php
namespace Wwwision\EventScheduler\Command;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Cli\CommandController;
use Wwwision\EventScheduler\EventScheduler;

class SchedulerCommandController extends CommandController
{
    /**
     * @Flow\Inject
     * @var EventScheduler
     */
    protected $scheduler;

    /**
     * Add a task to the scheduler
     *
     * @param string $id Unique id of the task - any task with the same id & type will be overridden!
     * @param string $type Arbitrary type of the task (for multi-tenancy)
     * @param \DateTimeImmutable $timestamp When should the task be triggered earliest
     * @param string|null $payload Arbitrary payload that will be accessible from the SchedulerWasTriggered event
     */
    public function addCommand(string $id, string $type, \DateTimeImmutable $timestamp, string $payload = null): void
    {
        if ($payload !== null) {
            $payload = json_decode($payload, true);
        }
        $this->scheduler->scheduleTask($id, $type, $timestamp, $payload);
    }

    /**
     * Remove a task from the scheduler
     *
     * @param string $id Unique id of the task to be removed
     * @param string $type Arbitrary type of the task (for multi-tenancy)
     */
    public function cancelCommand(string $id, string $type): void
    {
        $this->scheduler->cancelTask($id, $type);
    }

    /**
     * Execute all overdue tasks
     */
    public function runCommand(): void
    {
        $this->scheduler->run();
    }
}