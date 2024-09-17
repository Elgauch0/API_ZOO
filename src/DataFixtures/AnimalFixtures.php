<?php

namespace App\DataFixtures;

use App\Entity\Animal;
use App\Entity\Habitat;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AnimalFixtures extends Fixture
{



    public function load(ObjectManager $manager): void
    {
        $habitat1 = new Habitat();
        $habitat1->setNom('Jungle');
        $habitat1->setImage('habitats/habitat.pnj');
        $habitat1->setDescription('Description Jungle');
        $manager->persist($habitat1);
        $manager->flush();


        $animal1 = new Animal();
        $animal1->setPrenom('pikatchu');
        $animal1->setRace('Rat');
        $animal1->setImage('Animals/pihatchu.pnj');

        $animal1->setHabitat($habitat1);







        $manager->persist($animal1);
        $manager->flush();
    }
}
