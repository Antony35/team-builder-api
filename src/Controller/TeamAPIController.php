<?php

namespace App\Controller;

use App\Entity\People;
use App\Entity\Team;
use App\Repository\PeopleRepository;
use App\Repository\TeamRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Serializer\SerializerInterface;
#[Route('/api/team', name: 'api_team_')]
class TeamAPIController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TeamRepository $teamRepository,
        private readonly PeopleRepository $peopleRepository,
        private readonly SerializerInterface $serializer,
    )
    {
    }

    #[Route('/show', name: 'show', methods: ['GET'])]
    public function show(): JsonResponse
    {
        $data = $this->teamRepository->findAll();

        return $this->json($data,200, [], ['groups' => ['team']]);
    }

    #[Route('/add', name: 'add', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        $team = $this->serializer->deserialize($request->getContent(), Team::class, 'json');
        $this->entityManager->persist($team);
        $this->entityManager->flush();

        return $this->json(sprintf("%s has been added", $team->getName()));
    }

    #[Route('/edit/{id}', name: 'edit', methods: ['PATCH'])]
    public function edit(int $id, Request $request): JsonResponse
    {
        $team = $this->teamRepository->find($id);
        $teamName = $team->getName();

        $this->serializer->deserialize($request->getContent(), Team::class, 'json', ['object_to_populate' => $team]);

        $this->entityManager->flush();

        return $this->json(sprintf("%s has been update to %s",$teamName, $team->getName()));
    }

    #[Route('/delete/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Team $team): JsonResponse
    {
        $this->entityManager->remove($team);
        $this->entityManager->flush();

        return $this->json(sprintf("%s has been delete", $team->getName()));
    }

    #[Route('/remove/people/{teamId}/{peopleId}', name: 'remove_people',requirements: ['teamId' => Requirement::DIGITS, 'peopleId' => Requirement::DIGITS], methods: ['POST'])]
    public function removePeople(int $teamId, int $peopleId ): JsonResponse
    {
        $team = $this->teamRepository->find($teamId);
        $people = $this->peopleRepository->find($peopleId);

        $team->removePerson($people);
        $this->entityManager->flush();

        return $this->json(sprintf("%s has been remove of the team %s", $people->getFullName(), $team->getName()));
    }
}
