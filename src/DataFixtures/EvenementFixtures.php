<?php

namespace App\DataFixtures;

use App\Entity\Evenement;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;
use Symfony\Component\String\Slugger\SluggerInterface;

class EvenementFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private UserRepository $userRepo, private SluggerInterface $slugger)
    {}

    public function load(ObjectManager $manager): void
    {
        $users = $this->userRepo->findAll();

        $faker = Faker::create('fr_FR');

        for($i = 0; $i < 40; $i++)
        {
            $randomKey = array_rand($users);
            $user = $users[$randomKey];

            $evenement = new Evenement($this->slugger);
            $evenement
                ->setUser($user)
                ->setIntitule($faker->words(3, true))
                ->setDescription($faker->sentences(3, true))
                ->setSlug($this->slugger->slug($evenement->getIntitule()))
            ;

            $manager->persist($evenement);
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
