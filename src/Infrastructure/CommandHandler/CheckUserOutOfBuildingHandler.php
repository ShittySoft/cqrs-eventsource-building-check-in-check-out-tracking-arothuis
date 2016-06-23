<?php

namespace Building\Infrastructure\CommandHandler;

use Building\Domain\Aggregate\Building;
use Building\Domain\Command\CheckUserOutOfBuilding;
use Building\Domain\Repository\BuildingRepositoryInterface;
use Rhumsaa\Uuid\Uuid;

final class CheckUserOutOfBuildingHandler
{
    /**
     * @var BuildingRepositoryInterface
     */
    private $repository;

    public function __construct(BuildingRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(CheckUserOutOfBuilding $checkIn)
    {
        /** @var Building $building */
        $building = $this->repository->get(Uuid::fromString($checkIn->buildingId()));

        $building->checkOutUser($checkIn->username());

        $this->repository->add($building);
    }
}
