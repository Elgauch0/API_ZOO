<?php

namespace App\Controller;

use App\Entity\Habitat;
use App\Repository\HabitatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

#[Route('/api')]
class HabitatController extends AbstractController
{
    private $serializer;
    private $em;

    public function __construct(SerializerInterface $serializer, EntityManagerInterface $em)
    {
        $this->serializer = $serializer;
        $this->em = $em;
    }
    #[Route('/habitats', name: 'app_habitat', methods: ['GET'])]
    public function getAllHabitats(HabitatRepository $repohabi): JsonResponse
    {
        $habitats = $repohabi->findAll();
        $data = $this->serializer->serialize($habitats, 'json', ['groups' => 'habitat:read']);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    #[Route('/habitats/{id}', name: "Show_habitat", requirements: ['id' => Requirement::DIGITS], methods: ['GET'])]
    public function GetHabitat(Habitat $habitat): JsonResponse
    {
        $jsonHabitat = $this->serializer->serialize($habitat, 'json', ['groups' => 'habitat:read']);
        return new JsonResponse($jsonHabitat, Response::HTTP_OK, [], true);
    }

    #[Route('/habitats/add', name: 'Add_Habitat', methods: ['POST'])]
    public function AddHabitat(Request $request): JsonResponse
    {
        $habitat = $this->serializer->deserialize($request->getContent(), Habitat::class, 'json');
        $this->em->persist($habitat);
        $this->em->flush();

        return new JsonResponse(['status' => 'Habitat created!'], JsonResponse::HTTP_OK);
    }

    #[Route('/habitats/{id}', name: 'Delete_habitat', requirements: ['id' => Requirement::DIGITS], methods: ['DELETE'])]
    public function DeleteHabitat(Habitat $habitat): JsonResponse
    {
        $this->em->remove($habitat);
        $this->em->flush();
        return new JsonResponse(['status' => 'Habitat removed'], JsonResponse::HTTP_OK);
    }

    #[Route('/habitats/{id}', name: 'Updat_habitat', requirements: ['id' => Requirement::DIGITS], methods: ['PUT'])]
    public function EditHabitat(Habitat $habitat, Request $request): JsonResponse
    {
        $updatedh = $this->serializer->deserialize($request->getContent(), Habitat::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $habitat]);
        $this->em->flush();



        return new JsonResponse(['status' => 'Habitat updated'], JsonResponse::HTTP_OK);
    }
}
