<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Publication;
use App\Repository\UserRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class PublicationFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private UserRepository $userRepo, private SluggerInterface $slugger)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $users = $this->userRepo->findAll();
        /**
         * Creation des publications
         */
        for ($i = 0; $i < 50; $i++) {
            $publication = new Publication();
            $publication->setTitre($faker->words(5, true));
            $publication->setContenu($faker->text);
            $publication->setSlug($this->slugger->slug($publication->getTitre()));
            $publication->setUser($users[rand(0, count($users) - 1)]);
            
            $manager->persist($publication);
        }

        $manager->flush();
    }
    public function getDependencies()
    {
        return [
            UserFixtures::class
        ];
    }
}
