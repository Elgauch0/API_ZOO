<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Repository\AnimalRepository;
use App\Repository\HabitatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/animals')]
class AnimalController extends AbstractController
{
    private $em;
    private $serializer;
    public function __construct(EntityManagerInterface $em1, SerializerInterface $serializer1)
    {
        $this->serializer = $serializer1;
        $this->em = $em1;
    }

    #[Route('/', name: 'app_animal', methods: ['GET'])]
    public function GetAllAnimals(AnimalRepository $aniRepo): JsonResponse
    {
        $animals = $aniRepo->findAll();
        $data = $this->serializer->serialize($animals, 'json', ['groups' => 'animal:read']);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }


    #[Route('/{id}', name: 'Show_animal', requirements: ['id' => Requirement::DIGITS], methods: ['GET'])]
    public function ShowAnimal(Animal $animal): JsonResponse
    {
        $animaljson = $this->serializer->serialize($animal, 'json', ['groups' => 'animal:read']);
        return new JsonResponse($animaljson, Response::HTTP_OK, [], true);
    }


    #[Route('/{id}', name: 'Delete_animal', requirements: ['id' => Requirement::DIGITS], methods: ['DELETE'])]
    #[IsGranted(['ROLE_ADMIN', 'ROLE_EMPLOYE'])]
    public function DeleteAnimal(Animal $animal): JsonResponse
    {
        $this->em->remove($animal);
        $this->em->flush();
        return new JsonResponse(['status' => 'Animal removed'], Response::HTTP_OK);
    }


    #[Route('/{id}', name: 'Update_animal', requirements: ['id' => Requirement::DIGITS], methods: ['PUT'])]
    #[IsGranted(['ROLE_ADMIN', 'ROLE_EMPLOYE'])]
    public function EditAnimal(Animal $animal, Request $request, HabitatRepository $habitarepo, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $animal->setPrenom($data['prenom']);
        $animal->setRace($data['race']);
        $animal->setImage($data['image']);
        $habitat = $habitarepo->findOneBy(['id' => $data['habitat_id']]);
        if ($habitat) {
            $animal->setHabitat($habitat);
        } else {
            return new JsonResponse(['status' => 'Habitat not found'], Response::HTTP_BAD_REQUEST);
        }

        $errors = $validator->validate($animal);
        if ($errors->count() > 0) {
            throw new JsonResponse($this->serializer->serialize($errors, 'json'), Response::HTTP_BAD_REQUEST, [], true);
        }
        $this->em->flush();
        return new JsonResponse(null, Response::HTTP_OK, []);
    }


    #[Route('/add', name: 'Add_animal', methods: ['POST'])]
    #[IsGranted(['ROLE_ADMIN', 'ROLE_EMPLOYE'])]
    public function AddAnimal(Request $request, HabitatRepository $habitatRepo, ValidatorInterface $validator): JsonResponse
    {
        $animal = new Animal();
        $data = json_decode($request->getContent(), true);
        $animal->setPrenom($data['prenom']);
        $animal->setRace($data['race']);
        $animal->setImage($data['image']);
        if (isset($data['habitat_id'])) {
            $habitat = $habitatRepo->findOneBy(['id' => $data['habitat_id']]);
            if ($habitat) {
                $animal->setHabitat($habitat);
            } else {
                return new JsonResponse(['status' => 'Habitat  not found'], JsonResponse::HTTP_BAD_REQUEST);
            }
        }
        $errors = $validator->validate($animal);
        if ($errors->count() > 0) {
            throw new JsonResponse($this->serializer->serialize($errors, 'json'), Response::HTTP_BAD_REQUEST, [], true);
        }

        $this->em->persist($animal);
        $this->em->flush();


        return new JsonResponse(['status' => 'animal created!'], JsonResponse::HTTP_OK);
    }
}
