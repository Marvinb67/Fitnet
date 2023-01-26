<?php

namespace App\DataFixtures;


use DateTimeImmutable;
use Faker\Factory as Faker;
use App\DataFixtures\UserFixtures;
use App\Repository\UserRepository;
use App\Entity\InscriptionEvenement;
use App\DataFixtures\EvenementFixtures;
use App\Repository\EvenementRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class InscriptionEvenementFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private UserRepository $userRepo, 
        private EvenementRepository $evenementRepo
        )
    {}

    public function load(ObjectManager $manager): void
    {

        $users = $this->userRepo->findAll();
        $evenements = $this->evenementRepo->findAll();

        $faker = Faker::create('fr_FR');

        for($i = 0; $i < 130; $i ++)
        {    
            $randomKey = array_rand($users);
            $user = $users[$randomKey];
            $randomKey = array_rand($evenements);
            $evenement = $evenements[$randomKey];

            $start = $faker->dateTimeBetween('-1 year', 'now');
            $end = $faker->dateTimeBetween('now', '+1 year');

            $inscriptionEvenement = new InscriptionEvenement();
            $inscriptionEvenement
                ->setUser($user)
                ->setEvenement($evenement)
                ->setLieu($faker->randomNumber(2, false)." avenue du àtous " .$faker->secondaryAddress()." ".$faker->departmentName()." ".$faker->region())
                ->setNbPlaces($faker->randomNumber(2, false))
                ->setStartAt(DateTimeImmutable::createFromMutable($start))
                ->setEndAt(DateTimeImmutable::createFromMutable($end))
            ;

            $manager->persist($inscriptionEvenement);
        }

        $manager->flush();
    }
        
    public function getDependencies()
    {
        return array(
            UserFixtures::class,
            EvenementFixtures::class,
        );

    }
}
