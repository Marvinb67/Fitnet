<?php

namespace App\DataFixtures;

use App\Entity\Groupe;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Repository\UserRepository;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory as Faker;

class GroupFixtures extends Fixture implements DependentFixtureInterface
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Faker::create();

        $users = $this->userRepository->findAll();

        for($i = 0; $i < 10; $i++)
        {
            $randomKey = array_rand($users);
            $user = $users[$randomKey];

            $groupe = new Groupe();
            $groupe
                ->setUser($user)
                ->setIntitule($faker->words(3, true))
            ;

            $manager->persist($groupe);
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
