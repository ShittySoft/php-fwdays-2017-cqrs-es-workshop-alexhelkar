<?php

declare(strict_types=1);

namespace Building\Domain\DomainEvent;

use Prooph\EventSourcing\AggregateChanged;

final class NewUserCheckedIn extends AggregateChanged
{
    public function buildingId(): string
    {
        return $this->payload['buildingId'];
    }

    public function username(): string
    {
        return $this->payload['username'];
    }
}
