<?php

namespace App\DataFixtures;


use App\Entity\User;
use Faker\Factory as Faker;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    /**
     * Permet d'encoder le mot de passe de l'utilisateur
     * 
     * @var UserPasswordHasherInterface
     */
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Faker::create('fr_FR');

        for($i=0; $i <20; $i++)
        {
            $user = new User;
            $user
                ->setEmail($faker->email())
                ->setNom($faker->lastName())
                ->setPrenom($faker->firstName())
                ->setPassword($this->passwordHasher->hashPassword($user, '123456789'))
                ->setImage($faker->imageUrl(360, 360, 'user', true, ($user->getNom().' '.$user->getPrenom()), false, 'png'))
                ->setIsVerified(rand(0, 1))
            ;
            //  1er user avec role admin
            if ($i == 1) $user->setRoles(['ROLE_SUPER_ADMIN']);
            $manager->persist($user);
                
        }

        $manager->flush();

    }
}
