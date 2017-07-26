<?php
namespace Wwwision\EventScheduler\Domain\Model;

use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;

/**
 * NOTE: This class is only used in order to create the Doctrine schema for this table
 *
 * @Flow\Entity(readOnly=true)
 * @ORM\Table(name="wwwision_eventscheduler_task")
 */
class Task
{

    /**
     * @var string
     * @ORM\Id
     */
    public $id;

    /**
     * @var string
     * @ORM\Id
     */
    public $type;

    /**
     * @var \DateTimeInterface
     */
    public $timestamp;

    /**
     * @var array
     * @ORM\Column(type="json_array")
     */
    public $payload;
}