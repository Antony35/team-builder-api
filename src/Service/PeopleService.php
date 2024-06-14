<?php

namespace App\Service;

use App\Entity\People;
use App\Entity\Team;
use App\Repository\TeamRepository;
use Doctrine\ORM\EntityManagerInterface;

class PeopleService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TeamRepository $teamRepository,
    )
    {
    }

    public function createPeople(array $data): People
    {
        $people = new People();
        $people->setFirstname($data['firstname']);
        $people->setLastname($data['lastname']);
        dd($data);

        if (isset($data['team'])) {
            foreach ($data['team'] as $team) {
                $people->addTeam($this->teamRepository->find($team));
            }
        }
        $this->entityManager->persist($people);
        $this->entityManager->flush();

        return $people;
    }
}