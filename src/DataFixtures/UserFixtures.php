<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Repository\RoleRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class UserFixtures extends Fixture

{
    private $passwordHasher;
    private $roleRepository;

    public function __construct(UserPasswordHasherInterface $passwordHasher, RoleRepository $roleRepository)
    {
        $this->passwordHasher = $passwordHasher;
        $this->roleRepository = $roleRepository;
    }

    public function load(ObjectManager $manager): void
    {
        //ajout d un admin
        $admin = new User();
        $admin->setEmail('Admin@ADMin.com');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'password'));
        $admin->setPrenom('Admin_Prénom');
        $admin->setNom('Admin_Nom');
        /////////////////////
        // Ajout du Role

        $admin->addUserRole($this->getReference('role_admin'));

        //ajout d un veterinaire
        $user = new User();
        $user->setEmail('VETERINAIRE@ADMin.com');
        $user->setPassword($this->passwordHasher->hashPassword($admin, 'password'));
        $user->setPrenom('Veterinaire_Prénom');
        $user->setNom('veterinaire_Nom');
        ///////////////////// // Ajout du Role


        $user->addUserRole($this->getReference('role_vet'));
        //ajout d un employé
        $Employe = new User();
        $Employe->setEmail('Employe@ADMin.com');
        $Employe->setPassword($this->passwordHasher->hashPassword($admin, 'password'));
        $Employe->setPrenom('Employé_Prénom');
        $Employe->setNom('Employe_Nom');





        $manager->persist($user);
        $manager->persist($admin);
        $manager->persist($Employe);




        $manager->flush();
    }
    public function getDependencies()
    {
        return [
            RoleFixtures::class,
        ];
    }
}
