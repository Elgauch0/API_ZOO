<?php

namespace App\Controller;

use App\Entity\RapporVeterinaire;
use App\Repository\AnimalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\RapporVeterinaireRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    #[IsGranted(['ROLE_ADMIN', 'ROLE_VETERINAIRE'])]
    public function GetAllRapportVet(RapporVeterinaireRepository $RapportRepo): JsonResponse
    {
        $rapports = $RapportRepo->findAll();
        $data = $this->serializer->serialize($rapports, 'json', [
            'groups' => ["rapportVet:read"]
        ]);
        return new JsonResponse($data, Response::HTTP_OK, [], true);
    }

    #[Route('/add', name: 'add_RapportV', requirements: ['id' => Requirement::DIGITS], methods: ['POST'])]
    #[IsGranted('ROLE_VETERINAIRE')]
    public function CreatRapport(Request $request, AnimalRepository $animalrepo, ValidatorInterface $validator): JsonResponse
    {
        $rapport = new RapporVeterinaire();
        $data = json_decode($request->getContent(), true);
        $rapport->setEtat($data['etat']);
        $rapport->setNourriture($data['nourriture']);
        $rapport->setDate(new \DateTimeImmutable());
        $animal_id = $animalrepo->findOneBy(['id' => $data['animal_id']]);
        $rapport->setRapportsAnimal($animal_id);

        $errors = $validator->validate($rapport);
        if ($errors->count() > 0) {
            return new JsonResponse($this->serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $this->em->persist($rapport);
        $this->em->flush();
        $jsonrapport = $this->serializer->serialize($rapport, 'json', ['groups' => 'rapportVet:write']);

        return new JsonResponse($jsonrapport, Response::HTTP_OK, [], true);
    }


    #[Route('/{id}', name: 'Delete_Rapport', requirements: ['id' => Requirement::DIGITS], methods: ['DELETE'])]
    #[IsGranted('ROLE_VETERINAIRE')]
    public function DeleteRapport(RapporVeterinaire $rapportV): JsonResponse
    {
        $this->em->remove($rapportV);
        $this->em->flush();
        return new JsonResponse(['status' => 'Rapport removed'], Response::HTTP_OK);
    }


    #[Route('/{id}', name: 'show_Rapport', requirements: ['id' => Requirement::DIGITS], methods: ['GET'])]
    #[IsGranted(['ROLE_ADMIN', 'ROLE_VETERINAIRE'])]
    public function ShowRapport(RapporVeterinaire $rapporVe): JsonResponse
    {

        $rapportJson = $this->serializer->serialize($rapporVe, 'json', [
            'groups' => ['rapportVet:read']
        ]);
        return  new JsonResponse($rapportJson, Response::HTTP_OK, [], true);
    }
}
