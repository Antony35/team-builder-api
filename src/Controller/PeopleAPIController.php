<?php

namespace App\Controller;

use App\Entity\People;
use App\Repository\PeopleRepository;
use App\Repository\TeamRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/people', name: 'api_people')]
class PeopleAPIController extends AbstractController
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly PeopleRepository       $peopleRepository,
        private readonly TeamRepository         $teamRepository,
        private readonly SerializerInterface    $serializer,
    )
    {
    }

    #[Route('/show', name: 'show', methods: ['GET'])]
    public function show(): JsonResponse
    {
        $data = $this->peopleRepository->findAll();

        return $this->json($data, 200, [], ['groups' => ['people']]);
    }


    #[Route('/add', name: 'add', methods: ['POST'])]
    public function add(Request $request,
                        #[MapRequestPayload(
                            serializationContext: [
                                'groups' => ['people']
                            ]
                        )]
                        People  $people): JsonResponse
    {
        //TODO see DTO for improve team getting
        if (isset($request->toArray()['team_id'])) {
            $team = $this->teamRepository->find($request->toArray()['team_id']);
            $people->addTeam($team);
        }

        $this->entityManager->persist($people);
        $this->entityManager->flush();

        return $this->json(sprintf("%s has been added", $people->getFullName()));
    }

    #[Route('/edit/{id}', name: 'edit', requirements: ['id' => Requirement::DIGITS], methods: ['PATCH'])]
    public function edit(int $id, Request $request): JsonResponse
    {
        //TODO change response for the different edit like set name or team or all
        $people = $this->peopleRepository->find($id);
        $peopleName = $people->getFullName();

        $this->serializer->deserialize($request->getContent(), People::class, 'json', ['object_to_populate' => $people]);

        if (isset($request->toArray()['team_id'])) {
            $team = $this->teamRepository->find($request->toArray()['team_id']);
            $people->addTeam($team);
        }

        $this->entityManager->flush();

        return $this->json(sprintf("%s has been update to %s", $peopleName, $people->getFullName()));
    }

    #[Route('/delete/{id}', name: 'delete', requirements: ['id' => Requirement::DIGITS], methods: ['DELETE'])]
    public function delete(People $people): JsonResponse
    {
        $this->entityManager->remove($people);
        $this->entityManager->flush();

        return $this->json(sprintf("%s has been delete", $people->getFullName()));
    }
}
