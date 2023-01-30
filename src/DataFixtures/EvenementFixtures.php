<?php

namespace App\DataFixtures;

use App\Entity\Evenement;
use Faker\Factory as Faker;
use App\DataFixtures\TagFixtures;
use App\Repository\TagRepository;
use App\Repository\UserRepository;
use App\DataFixtures\MediaFixtures;
use App\Repository\MediaRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class EvenementFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private UserRepository $userRepo,
        private TagRepository $tagRepo,  
        private MediaRepository $mediaRepo,  
        private SluggerInterface $slugger)
    {}

    public function load(ObjectManager $manager): void
    {
        $users = $this->userRepo->findAll();
        $tags = $this->tagRepo->findAll();
        $media = $this->mediaRepo->findAll();

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
            $evenements[] = $evenement;
        }

        foreach($evenements as $evenement){
            $max = rand(0, 5);
            shuffle($evenements);
            shuffle($tags);
            for($i=0; $i<$max; $i++){
                $evenement->addTagsEvenement($tags[array_rand($tags)]);
                $evenement->addMediaEvenement($media[array_rand($media)]);
            }
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            UserFixtures::class,
            TagFixtures::class,
            MediaFixtures::class,
        );

    }
}
