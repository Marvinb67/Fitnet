<?php

namespace App\DataFixtures;


use App\DataFixtures\UserFixtures;
use App\Repository\UserRepository;
use App\Entity\ReactionPublication;
use Doctrine\Persistence\ObjectManager;
use App\DataFixtures\PublicationFixtures;
use App\Repository\PublicationRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ReactionPublicationFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private UserRepository $userRepo,
        private PublicationRepository $publicationRepo
    ) {
    }

    public function load(ObjectManager $manager): void
    {

        $users = $this->userRepo->findAll();
        $publications = $this->publicationRepo->findAll();

        $reactions = [];

        for ($i = 0; $i < 100; $i++) {
            $randomKey = array_rand($users);
            $user = $users[$randomKey];
            $randomKey = array_rand($publications);
            $publication = $publications[$randomKey];

            if ($i < 2) {
                $reactionPublication = new ReactionPublication();
                $reactionPublication
                    ->setUser($user)
                    ->setPublication($publication)
                    ->setEtatLikeDislike(rand(0, 1));
            }
            if ($i >= 2) {
                foreach ($reactions as $reaction) {
                    if (!property_exists($reaction, $user->getId()) && !property_exists($reaction, $publication->getId())) {
                        $reactionPublication = new ReactionPublication();
                        $reactionPublication
                            ->setUser($user)
                            ->setPublication($publication)
                            ->setEtatLikeDislike(rand(0, 1));
                    }
                }
            }

            $manager->persist($reactionPublication);
            $reactions[] = $reactionPublication;
        }
        // var_dump($reactions[0]->getUser());
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            UserFixtures::class,
            PublicationFixtures::class,
        );
    }
}
