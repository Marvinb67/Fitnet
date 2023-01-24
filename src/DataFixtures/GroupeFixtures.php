<?php

namespace App\DataFixtures;

use App\Entity\Groupe;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Repository\UserRepository;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory as Faker;
use Symfony\Component\String\Slugger\SluggerInterface;

class GroupeFixtures extends Fixture implements DependentFixtureInterface
{

    public function __construct(private UserRepository $userRepository, private SluggerInterface $slugger)
    {}

    public function load(ObjectManager $manager): void
    {
        $faker = Faker::create();

        $users = $this->userRepository->findAll();

        for($i = 0; $i < 10; $i++)
        {
            $randomKey = array_rand($users);
            $user = $users[$randomKey];

            $groupe = new Groupe($this->slugger);
            $groupe
                ->setUser($user)
                ->setIntitule($faker->words(3, true))
                ->setSlug($this->slugger->slug($groupe->getIntitule()))
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
