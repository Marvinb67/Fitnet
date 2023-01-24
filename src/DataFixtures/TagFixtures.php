<?php

namespace App\DataFixtures;

use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Repository\UserRepository;
use Faker\Factory as Faker;
use Symfony\Component\String\Slugger\SluggerInterface;

class TagFixtures extends Fixture
{

    public function __construct(private UserRepository $userRepository, private SluggerInterface $slugger)
    {}

    public function load(ObjectManager $manager): void
    {
        $faker = Faker::create();

        $users = $this->userRepository->findAll();

        for($i = 0; $i < 50; $i++)
        {
            $randomKey = array_rand($users);
            $user = $users[$randomKey];

            $Tag = new Tag($this->slugger);
            $Tag
                ->setIntitule('#'.$faker->word())
            ;

            $manager->persist($Tag);
        }

        $manager->flush();
    }
}
