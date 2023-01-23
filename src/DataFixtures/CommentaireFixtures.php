<?php

namespace App\DataFixtures;


use App\Entity\Commentaire;
use Faker\Factory as Faker;
use App\DataFixtures\UserFixtures;
use App\Repository\UserRepository;
use Doctrine\Persistence\ObjectManager;
use App\Repository\PublicationRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CommentaireFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private UserRepository $userRepo, private PublicationRepository $publicationRepo)
    {}

    public function load(ObjectManager $manager): void
    {

        $users = $this->userRepo->findAll();
        $publications = $this->publicationRepo->findAll();

        $faker = Faker::create('fr_FR');
        $commentaires = [];
        for($i = 0; $i < 30; $i ++)
        {    
            $randomKey = array_rand($users);
            $randomKeyPublication = array_rand($publications);
            $user = $users[$randomKey];
            $publication = $publications[$randomKeyPublication];

            $commentaire = new Commentaire();
            $commentaire
                ->setUser($user)
                ->setPublication($publication)
                ->setContenu($faker->sentences(3, true))
            ;

            $manager->persist($commentaire);
            array_unshift($commentaires, $commentaire);
        }

        for($i = 0; $i < 30; $i ++)
        {    
            $randomKey = array_rand($users);
            $randomKeyPublication = array_rand($publications);
            $user = $users[$randomKey];
            $publication = $publications[$randomKeyPublication];

            $commentaire = new Commentaire();
            $commentaire
                ->setUser($user)
                ->setPublication($publication)
                ->setContenu($faker->sentences(3, true))
                ->setParent($commentaires[array_rand($commentaires)])
            ;

            $manager->persist($commentaire);
        }

        $manager->flush();
    }
        
    public function getDependencies()
    {
        return array(
            UserFixtures::class,
            PublicationFixtures::class,
        );

    }
}
