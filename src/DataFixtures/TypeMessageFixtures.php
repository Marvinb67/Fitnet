<?php

namespace App\DataFixtures;

use App\Entity\TypeMessage;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TypeMessageFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $typeMessage1 = new TypeMessage();
        $typeMessage1->setIntitule('Demande d\'ami');

        $typeMessage2 = new TypeMessage();
        $typeMessage2->setIntitule('Invitation groupe');

        $typeMessage3 = new TypeMessage();
        $typeMessage3->setIntitule('Message privé');

        $manager->persist($typeMessage1);
        $manager->persist($typeMessage2);
        $manager->persist($typeMessage3);

        $manager->flush();
    }
}
