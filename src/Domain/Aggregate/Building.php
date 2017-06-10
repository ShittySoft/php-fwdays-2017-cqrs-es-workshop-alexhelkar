<?php

declare(strict_types=1);

namespace Building\Domain\Aggregate;

use Building\Domain\DomainEvent\NewBuildingWasRegistered;
use Building\Domain\DomainEvent\UserCheckedIn;
use Building\Domain\DomainEvent\UserCheckedOut;
use Prooph\EventSourcing\AggregateRoot;
use Rhumsaa\Uuid\Uuid;
use Symfony\Component\Serializer\Exception\LogicException;

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

    /**
     * @var array
     */
    private $checkedInUsers = false;

    public static function new(string $name) : self
    {
        $self = new self();

        $self->recordThat(NewBuildingWasRegistered::occur(
            (string) Uuid::uuid4(),
            [
                'name' => $name
            ]
        ));

        return $self;
    }

    public function checkInUser(string $username)
    {
        if(array_key_exists($username, $this->checkedInUsers)){
            throw new \DomainException('This user is already checked in');
        }

        $this->recordThat(UserCheckedIn::with($this->uuid, $username));
    }

    public function checkOutUser(string $username)
    {
        if(!array_key_exists($username, $this->checkedInUsers)){
            throw new \DomainException('This user is not checked in');
        }

        $this->recordThat(UserCheckedOut::with($this->uuid, $username));
    }

    public function whenNewBuildingWasRegistered(NewBuildingWasRegistered $event)
    {
        $this->uuid = $event->uuid();
        $this->name = $event->name();
    }

    /**
     * @param UserCheckedIn $event
     */
    public function whenUserCheckedIn(UserCheckedIn $event) : void
    {
        $this->checkedInUsers[$event->username()] = null;
    }

    /**
     * @param UserCheckedOut $event
     */
    public function whenUserCheckedOut(UserCheckedOut $event) : void
    {
        unset($this->checkedInUsers[$event->username()]);
    }

    /**
     * {@inheritDoc}
     */
    protected function aggregateId() : string
    {
        return (string) $this->uuid;
    }

    /**
     * {@inheritDoc}
     */
    public function id() : string
    {
        return $this->aggregateId();
    }
}
