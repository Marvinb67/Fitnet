<?php

namespace App\DataFixtures;


use DateTimeImmutable;
use App\Entity\Publication;
use Faker\Factory as Faker;
use App\DataFixtures\UserFixtures;
use App\Repository\UserRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class PublicationFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private UserRepository $userRepo, private SluggerInterface $sluggerInterface)
    {}

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
                ->setSlug($this->sluggerInterface->slug($publication->getTitre()))
                ->setContenu($faker->sentences(3, true))
                ->setCreatedAt(DateTimeImmutable::createFromMutable($faker->dateTime($max = 'now')))
                ->setEditedAt(DateTimeImmutable::createFromMutable($faker->dateTimeBetween($max = 'now')))
                ->setIsActive(rand(0, 1))
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
