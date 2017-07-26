<?php
namespace Wwwision\EventScheduler;

use Doctrine\Common\Persistence\ObjectManager as DoctrineObjectManager;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager as DoctrineEntityManager;
use Neos\EventSourcing\Event\EventPublisher;
use Neos\Flow\Annotations as Flow;
use Wwwision\EventScheduler\Event\SchedulerWasTriggered;

/**
 * @Flow\Scope("singleton")
 */
class EventScheduler
{
    /**
     * @var Connection
     */
    private $dbal;

    /**
     * @Flow\Inject
     * @var EventPublisher
     */
    protected $eventPublisher;

    /**
     * @param DoctrineObjectManager $entityManager
     * @return void
     */
    public function injectEntityManager(DoctrineObjectManager $entityManager)
    {
        if ($entityManager instanceof DoctrineEntityManager) {
            $this->dbal = $entityManager->getConnection();
        }
    }

    /**
     * @param string $id Id of the task to add, unique per type
     * @param string $type Arbitrary type to categorize the task with
     * @param \DateTimeInterface $timestamp When to execute the task
     * @param array|null $payload Arbitrary payload (json serializable) to attach to the task
     */
    public function scheduleTask(string $id, string $type, \DateTimeInterface $timestamp, array $payload = null): void
    {
        $this->startTransaction();
        $this->dbal->executeUpdate('REPLACE INTO wwwision_eventscheduler_task (id, type, timestamp, payload) VALUES (:id, :type, :timestamp, :payload)', [
            'id' => $id,
            'type' => $type,
            'timestamp' => $timestamp,
            'payload' => $payload !== null ? json_encode($payload) : null,
        ],
        [
            'timestamp' => Type::DATETIME,
        ]);
        $this->commitTransaction();
    }

    public function cancelTask(string $id, string $type): void
    {
        $this->startTransaction();
        $this->deleteTask($id, $type);
        $this->commitTransaction();
    }

    public function run(): void
    {
        $this->startTransaction();
        foreach ($this->dbal->fetchAll('SELECT * FROM wwwision_eventscheduler_task WHERE timestamp <= NOW()') as $task) {
            $payload = $task['payload'] !== null ? json_decode($task['payload'], true) : null;
            $event = new SchedulerWasTriggered($task['id'], $task['type'], \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $task['timestamp']), $payload);
            $this->eventPublisher->publish('Wwwision.EventScheduler:Process', $event);
            $this->deleteTask($task['id'], $task['type']);
        }
        $this->commitTransaction();
    }

    public function deleteTask(string $id, string $type): void
    {
        $this->dbal->executeUpdate('DELETE FROM wwwision_eventscheduler_task WHERE id = :id AND type = :type', [
            'id' => $id,
            ':type' => $type
        ]);
    }

    private function startTransaction(): void
    {
        $this->dbal->setTransactionIsolation(Connection::TRANSACTION_SERIALIZABLE);
        $this->dbal->beginTransaction();
    }

    private function commitTransaction(): void
    {
        $this->dbal->commit();
    }
}