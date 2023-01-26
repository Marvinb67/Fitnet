<?php

namespace App\DataFixtures;


use DateTimeImmutable;
use App\Entity\Publication;
use Faker\Factory as Faker;
use App\DataFixtures\TagFixtures;
use App\Repository\TagRepository;
use App\DataFixtures\UserFixtures;
use App\Repository\UserRepository;
use App\DataFixtures\MediaFixtures;
use App\Repository\MediaRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class PublicationFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private UserRepository $userRepo, 
        private TagRepository $tagRepo, 
        private MediaRepository $mediaRepo,  
        private SluggerInterface $sluggerInterface
        )
    {}

    public function load(ObjectManager $manager): void
    {

        $users = $this->userRepo->findAll();
        $tags = $this->tagRepo->findAll();
        $media = $this->mediaRepo->findAll();

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
            ;

            $manager->persist($publication);
            $publications[] = $publication;
        }

        foreach($publications as $publication){
            $max = rand(0, 5);
            shuffle($publications);
            shuffle($tags);
            for($i=0; $i<$max; $i++){
                $publication->addTag($tags[array_rand($tags)]);
                $publication->addMediaPublication($media[array_rand($media)]);
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
