<?php

namespace App\DataFixtures;


use DateTimeImmutable;
use Faker\Factory as Faker;
use App\DataFixtures\UserFixtures;
use App\Repository\UserRepository;
use App\Entity\ProgrammationEvenement;
use App\DataFixtures\EvenementFixtures;
use App\Repository\EvenementRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class InscriptionEvenementFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private EvenementRepository $evenementRepo, private UserRepository $userRepo)
    {}

    public function load(ObjectManager $manager): void
    {

        $evenements = $this->evenementRepo->findAll();
        $users = $this->userRepo->findAll();

        $faker = Faker::create('fr_FR');

        for($i = 0; $i < 130; $i ++)
        {
            $randomKey = array_rand($evenements);
            $evenement = $evenements[$randomKey];

            $randomUser = array_rand($users);
            $user = $users[$randomUser];

            $start = $faker->dateTimeBetween('-1 year', 'now');
            $end = $faker->dateTimeBetween('now', '+1 year');

            $inscriptionEvenement = new ProgrammationEvenement();
            $inscriptionEvenement
                ->setEvenement($evenement)
                ->setLieu($faker->randomNumber(2, false)." avenue du àtous " .$faker->secondaryAddress()." ".$faker->departmentName()." ".$faker->region())
                ->setNbPlaces($faker->randomNumber(2, false))
                ->setStartAt(DateTimeImmutable::createFromMutable($start))
                ->setEndAt(DateTimeImmutable::createFromMutable($end))
                ->addInscritEvenement($user);
            ;

            $manager->persist($inscriptionEvenement);
        }

        

        $manager->flush();
    }
        
    public function getDependencies()
    {
        return array(
            EvenementFixtures::class,
        );

    }
}
