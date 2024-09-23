<?php

namespace App\Controller;

use App\Entity\RapporVeterinaire;
use App\Repository\AnimalRepository;
use App\Repository\RapporVeterinaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Serializer\SerializerInterface;


#[Route('api/rapports')]
class RapportVetController extends AbstractController
{
    private $em;
    private $serializer;
    public function __construct(SerializerInterface $serializer, EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->serializer = $serializer;
    }



    #[Route('/', name: 'app_rapports', methods: ['GET'])]
    public function GetAllRapportVet(RapporVeterinaireRepository $RapportRepo): JsonResponse
    {
        $rapports = $RapportRepo->findAll();
        $data = $this->serializer->serialize($rapports, 'json', [
            'groups' => ["rapportVet:read"]
        ]);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    #[Route('/add', name: 'add_RapportV', requirements: ['id' => Requirement::DIGITS], methods: ['POST'])]
    public function CreatRapport(Request $request, AnimalRepository $animalrepo): JsonResponse
    {
        $rapport = new RapporVeterinaire();
        $data = json_decode($request->getContent(), true);
        $rapport->setEtat($data['etat']);
        $rapport->setNourriture($data['nourriture']);
        $rapport->setDate(new \DateTimeImmutable());
        $animal_id = $animalrepo->findOneBy(['id' => $data['animal_id']]);
        $rapport->setRapportsAnimal($animal_id);
        $this->em->persist($rapport);
        $this->em->flush();
        $jsonrapport = $this->serializer->serialize($rapport, 'json', ['groups' => 'rapportVet:write']);

        return new JsonResponse($jsonrapport, Response::HTTP_OK, [], true);
    }


    #[Route('/{id}', name: 'Delete_Rapport', requirements: ['id' => Requirement::DIGITS], methods: ['DELETE'])]
    public function DeleteRapport(RapporVeterinaire $rapportV): JsonResponse
    {
        $this->em->remove($rapportV);
        $this->em->flush();
        return new JsonResponse(['status' => 'Rapport removed'], Response::HTTP_OK);
    }


    #[Route('/{id}', name: 'show_Rapport', requirements: ['id' => Requirement::DIGITS], methods: ['GET'])]
    public function ShowRapport(RapporVeterinaire $rapporVe): JsonResponse
    {

        $rapportJson = $this->serializer->serialize($rapporVe, 'json', [
            'groups' => ['rapportVet:read']
        ]);
        return  new JsonResponse($rapportJson, Response::HTTP_OK, [], true);
    }
}
