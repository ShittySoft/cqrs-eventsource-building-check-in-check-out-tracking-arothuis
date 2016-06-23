<?php

namespace Building\Domain\Aggregate;

use Building\Domain\DomainEvent\NewBuildingWasRegistered;
use Building\Domain\DomainEvent\UserCheckedIntoBuilding;
use InvalidArgumentException;
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

    /**
     * @var string[]
     */
    private $checkedInUsers;

    public static function new($name) : self
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
        if (in_array($username, $this->checkedInUsers, true)) {
            throw new InvalidArgumentException('User is already checked in');
        }

        $this->recordThat(UserCheckedIntoBuilding::occur(
            $this->id(),
            [
                'username' => $username
            ]
        ));
    }

    public function checkOutUser(string $username)
    {
    }

    public function whenNewBuildingWasRegistered(NewBuildingWasRegistered $event)
    {
        $this->uuid = $event->uuid();
        $this->name = $event->name();
    }

    public function whenUserCheckedIntoBuilding(UserCheckedIntoBuilding $event)
    {
        $this->checkedInUsers[] = $event->username();

        // Guarantee uniqueness over time (when the constraint was not enforced yet)
        $this->checkedInUsers = array_unique($this->checkedInUsers);
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
