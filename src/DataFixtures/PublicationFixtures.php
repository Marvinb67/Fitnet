<?php

namespace App\DataFixtures;

use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;
use App\DataFixtures\UserFixtures;
use App\Entity\Publication;
use App\Repository\UserRepository;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class PublicationFixtures extends Fixture implements DependentFixtureInterface
{
    private $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function load(ObjectManager $manager): void
    {
        $users = $this->userRepo->findAll();

        $faker = Faker::create('fr_FR');

        for($i = 0; $i < 30; $i ++)
        {    
            $randomKey = array_rand($users);
            $user = $users[$randomKey];

            $publication = new Publication();
            $publication
                ->setUser($user)
                ->setTitre($faker->words(3, true))
                ->setContenu($faker->sentences(3, true))
                ->setCreatedAt(DateTimeImmutable::createFromMutable($faker->dateTime($max = 'now')))
                ->setSlug($faker->slug())
                ->setEditedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween($max = 'now')))
            ;

            $manager->persist($publication);
        }

        $manager->flush();
    }
        
    public function getDependencies()
    {
        return array(
            UserFixtures::class,
        );
    }
}
