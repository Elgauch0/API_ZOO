<?php

namespace App\DataFixtures;

use App\Entity\Role;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class RoleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $role1 = new Role();
        $role1->setLabel('Role_ADMIN');



        $role2 = new Role();
        $role2->setLabel('ROLE_VETERINAIRE');


        $manager->persist($role1);
        $this->addReference('role_admin', $role1);
        $manager->persist($role2);
        $this->addReference('role_vet', $role2);



        $manager->flush();
    }
}
