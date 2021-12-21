<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;

class UserFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setUsername('test');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->container->get('security.password_encoder')->encodePassword($user, 'test'));

        $manager->persist($user);
        $manager->flush();
    }
}
