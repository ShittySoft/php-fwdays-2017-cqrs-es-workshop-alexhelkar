<?php

declare(strict_types=1);

namespace Building\Domain\Aggregate;

use Building\Domain\DomainEvent\NewBuildingWasRegistered;
use Building\Domain\DomainEvent\NewUserCheckedIn;
use Prooph\EventSourcing\AggregateRoot;
use Rhumsaa\Uuid\Uuid;

final class Building extends AggregateRoot
{
    /**
     * @var Uuid
     */
    private $uuid;

    /**
     * @var string
     */
    private $name;

    public static function new(string $name): self
    {
        $self = new self();

        $self->recordThat(NewBuildingWasRegistered::occur(
            (string) Uuid::uuid4(),
            [
                'name' => $name,
            ]
        ));

        return $self;
    }

    public function checkInUser(string $username): void
    {
        $this->recordThat(NewUserCheckedIn::occur(
            (string) Uuid::uuid4(),
            [
                'username' => $username,
                'buildingId' => (string) $this->uuid,
            ]
        ));
    }

    public function checkOutUser(string $username)
    {
        // @TODO to be implemented
    }

    public function whenNewBuildingWasRegistered(NewBuildingWasRegistered $event)
    {
        $this->uuid = $event->uuid();
        $this->name = $event->name();
    }

    public function whenNewUserCheckedIn(NewUserCheckedIn $event)
    {

    }

    /**
     * {@inheritDoc}
     */
    protected function aggregateId(): string
    {
        return (string) $this->uuid;
    }

    /**
     * {@inheritDoc}
     */
    public function id(): string
    {
        return $this->aggregateId();
    }
}
