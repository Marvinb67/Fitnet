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

        for ($i = 0; $i < 200; $i++) {
            $randomKey = array_rand($users);
            $user = $users[$randomKey];
            $randomKey = array_rand($publications);
            $publication = $publications[$randomKey];


            if (!empty($reactions)) {
                foreach ($reactions as $reaction) {
                    if ($reaction->getUser()->getId() != $user->getId() && $reaction->getPublication()->getId() != $publication->getId()) {
                        $reactionPublication = new ReactionPublication();
                        $reactionPublication
                            ->setUser($user)
                            ->setPublication($publication)
                            ->setEtatLikeDislike(rand(0, 1));
                    }
                }
            }
            $reactionPublication = new ReactionPublication();
            $reactionPublication
                ->setUser($user)
                ->setPublication($publication)
                ->setEtatLikeDislike(rand(0, 1));

            $reactions[] = $reactionPublication;
        }
        foreach ($reactions as $reaction) $manager->persist($reaction);

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
