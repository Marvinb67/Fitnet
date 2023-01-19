<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $encoder){}
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        /**
         * Create 10 users avec publications, commentaires, evenements
         */
        for ($i = 1; $i <= 10; $i++) {
            $user = User::create(
                $faker->safeEmail,
                $faker->lastname,
                $faker->firstname
            );
            // hash du mot de passe
            $user->setPassword($this->encoder->hashPassword($user, '123456789'));
            $user->setImage($faker->imageUrl(360, 360, 'user', true, ($user->getNom().' '.$user->getPrenom()), false, 'png'));
            //  1er user avec role admin
            if ($i == 1) $user->setRoles(['ROLE_SUPER_ADMIN']);
            $manager->persist($user);

        }
            $manager->flush();
    }
}
