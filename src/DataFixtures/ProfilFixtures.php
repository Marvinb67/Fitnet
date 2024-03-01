<?php

namespace App\DataFixtures;


use App\Entity\Profil;
use Faker\Factory as Faker;
use App\Repository\UserRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ProfilFixtures extends Fixture implements DependentFixtureInterface
{
        public function __construct(
            private UserRepository $userRepo, 
            )
        {}
    
    public function load(ObjectManager $manager): void
    {
        $users = $this->userRepo->findAll();
        $faker = Faker::create('fr_FR');

        for ($i = 0; $i < 20; $i++) {
            $profil = new Profil;
            $profil
                ->setBiographie($faker->paragraph())
                ->setAge($faker->dateTimeBetween('-60 years', '-15 years'))
                ->setJob($faker->word())
                ->setUser($users[$i]);
                
            $manager->persist($profil);
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
