<?php

namespace App\DataFixtures;

use App\Entity\Media;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Repository\UserRepository;
use Faker\Factory as Faker;
use Symfony\Component\String\Slugger\SluggerInterface;

class MediaFixtures extends Fixture
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

            $media = new Media($this->slugger);
            $media
                ->setTitre($faker->words(3, true))
                ->setDescription($faker->words(6, true))
                ->setLien($faker->imageUrl(640, 480, 'animals', true))
                ->setSlug($this->slugger->slug($media->getTitre()))
            ;

            $manager->persist($media);
        }

        $manager->flush();
    }
}
