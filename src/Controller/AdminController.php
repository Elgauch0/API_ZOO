<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\User;
use Psr\Log\LoggerInterface;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminController extends AbstractController
{
    // injection dependancies so the code stay clean and readbal----------------------------------->
    private $serializer;
    private $em;
    private $passwordHasher;
    public function __construct(SerializerInterface $serializer, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher)
    {
        $this->serializer = $serializer;
        $this->em = $em;
        $this->passwordHasher = $passwordHasher;
    }
    //All users-------------------------------------------------------------------------------->
    #[Route('api/users', name: 'AllUsers', methods: ['GET'])]
    public function getAllUsers(UserRepository $RepoUser): JsonResponse
    {

        $users = $RepoUser->findAll();
        $data = $this->serializer->serialize($users, 'json', ['groups' => 'user:read']);
        return new JsonResponse($data, 200, [], true);
    }

    //SHow a User -------------------------------------------------------------------------------->
    #[Route('/api/Users/{id}', name: "ShowUser", requirements: ['id' => '\d+'], methods: ['GET'])]
    public function ShowUser(User $user): JsonResponse
    {
        try {
            $jsonUser = $this->serializer->serialize($user, 'json', ['groups' => 'user:read']);
            return new JsonResponse($jsonUser, Response::HTTP_OK, [], true);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'An error occurred while serializing the user data.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    //Delete a  User -------------------------------------------------------------------------------->
    #[Route('/api/users/{id}', name: "DeleteUser", requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function SuppUser(User $user): JsonResponse
    {
        $this->em->remove($user);
        $this->em->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
    //Add a  User -------------------------------------------------------------------------------->
    #[Route('/api/add', name: "AddUser", methods: ['POST'])]
    public function AddUser(Request $request): JsonResponse
    {
        $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');


        $hashedPassword = $this->passwordHasher->hashPassword($user, $user->getPassword());
        $user->setPassword($hashedPassword);

        $data = json_decode($request->getContent(), true);
        if (isset($data['veterinaire']) && $data['veterinaire'] === true) {

            $roleVeterinaire = $this->em->getRepository(Role::class)->findOneBy(['label' => 'ROLE_VETERINAIRE']);
            $user->addUserRole($roleVeterinaire);
        }


        $this->em->persist($user);
        $this->em->flush();
        $jsonUser = $this->serializer->serialize($user, 'json', ['groups' => 'user:create']);
        return new JsonResponse($jsonUser, Response::HTTP_CREATED, [], true);
    }
}
