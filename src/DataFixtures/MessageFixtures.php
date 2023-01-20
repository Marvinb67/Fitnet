<?php

namespace App\DataFixtures;

use DateTimeImmutable;
use App\Entity\Message;
use Faker\Factory as Faker;
use App\DataFixtures\UserFixtures;
use App\Repository\UserRepository;
use Doctrine\Persistence\ObjectManager;
use App\Repository\TypeMessageRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class MessageFixtures extends Fixture implements DependentFixtureInterface
{
    private $userRepository;
    private $typeMessageRepository;

    public function __construct(UserRepository $userRepository, TypeMessageRepository $typeMessageRepository)
    {
        $this->userRepository = $userRepository;
        $this->typeMessageRepository = $typeMessageRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Faker::create();

        $users = $this->userRepository->findAll();

        $typesMessage = $this->typeMessageRepository->findAll();

        for($i = 0; $i < 30; $i++)
        {
            $randomKey = array_rand($users);
            $user = $users[$randomKey];

            $randomKeyTypeMess = array_rand($typesMessage);
            $typeMessage = $typesMessage[$randomKeyTypeMess];

            $message = new Message();
            $message
                ->setUser($user)
                ->setTypeMessage($typeMessage)
                ->setContenu($faker->sentences(3, true))
                ->setCreatedAt(DateTimeImmutable::createFromMutable($faker->dateTime($max = 'now')))
            ;

            $manager->persist($message);
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
