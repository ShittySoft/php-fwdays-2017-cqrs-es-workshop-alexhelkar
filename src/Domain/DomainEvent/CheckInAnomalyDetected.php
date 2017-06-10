<?php

namespace Building\Domain\DomainEvent;

use Prooph\EventSourcing\AggregateChanged;
use Rhumsaa\Uuid\Uuid;

/**
 * Class CheckInAnomalyDetected
 */
class CheckInAnomalyDetected extends AggregateChanged
{
    public static function with(Uuid $buildingId, string $username): self
    {
        return self::occur($buildingId->toString(), ['username' => $username]);
    }

    public function buildingId(): Uuid
    {
        return Uuid::fromString($this->aggregateId());
    }

    public function username(): string
    {
        return $this->payload['username'];
    }
}