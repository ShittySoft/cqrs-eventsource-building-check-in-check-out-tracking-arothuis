<?php

namespace Building\Infrastructure\CommandHandler;

use Building\Domain\Aggregate\Building;
use Building\Domain\Command\CheckUserIntoBuilding;
use Building\Domain\Repository\BuildingRepositoryInterface;
use Rhumsaa\Uuid\Uuid;

final class CheckUserIntoBuildingHandler
{
    /**
     * @var BuildingRepositoryInterface
     */
    private $repository;

    public function __construct(BuildingRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(CheckUserIntoBuilding $checkIn)
    {
        /** @var Building $building */
        $building = $this->repository->get(Uuid::fromString($checkIn->buildingId()));

        $building->checkInUser($checkIn->username());

        $this->repository->add($building);
    }
}
