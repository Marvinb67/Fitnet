<?php

namespace App\DataFixtures;


use App\Entity\User;
use Faker\Factory as Faker;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class UserFixtures extends Fixture
{
    /**
     * Permet d'encoder le mot de passe de l'utilisateur
     * 
     * @var UserPasswordHasherInterface
     */

    public function __construct(private UserPasswordHasherInterface $passwordHasher, private SluggerInterface $sluggerInterface)
    {}

    public function load(ObjectManager $manager): void
    {
        $faker = Faker::create('fr_FR');

        for ($i = 0; $i < 20; $i++) {
            $user = new User;
            $user
                ->setEmail($faker->email())
                ->setNom($faker->lastName())
                ->setPrenom($faker->firstName())
                ->setPassword($this->passwordHasher->hashPassword($user, '123456789'))
                ->setImage($faker->imageUrl(360, 360, 'user', true, ($user->getNom() . ' ' . $user->getPrenom()), false, 'png'))
                ->setIsVerified(rand(0, 1))
                ->setSlug($this->sluggerInterface->slug(strtolower($user->getNom().' '.$user->getPrenom())));
            //  1er user avec role admin
            if ($i == 1) $user->setRoles(['ROLE_SUPER_ADMIN']);
            $manager->persist($user);
            $users[] = $user;
            shuffle($users);
        }
        // pour chaque user on ajout une list d'amis
        foreach ($users as $user) {
            $amis = $this->randUsers($users);
            $follows =  $this->randUsers($users);
            $myFollows =  $this->randUsers($users);

            foreach($amis as $ami){
                if ($ami !== $user) $user->addAmi($ami);
            }

            foreach($follows as $follow){
                if ($follow !== $user) $user->addFollowUser($follow);
            }

            foreach($myFollows as $myFollow){
                if ($myFollow !== $user) $user->addFollowedByUser($myFollow);
            }

        }
        $manager->flush();
    }

    private function randUsers(array $arr)
    {
        for($i=0; $i<5; $i++){
            $amis[] = $arr[$i];
            shuffle($arr);
        }
        return $amis;
    }
}
