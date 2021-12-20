<?php

namespace App\Tests;

use App\Entity\User;
use App\Security\TokenStorageDecorator;
use Doctrine\ORM\EntityManagerInterface;

trait SecurityTrait
{
    protected static array $users = [];

    protected function login(string $role = 'ROLE_USER', bool $userFromDatabase = false): User
    {
        $user = $this->getUser($role, $userFromDatabase);

        $tokenStorage = self::$container->get('security.token_storage');

        /** @var TokenStorageDecorator $tokenStorage */
        if ($tokenStorage instanceof TokenStorageDecorator) {
            $tokenStorage->setUser($user);
        } else {
            $tokenStorage->setToken(
                TokenStorageDecorator::getNewToken($user)
            );
        }

        return$tokenStorage->getToken()->getUser();
    }

    public function logout()
    {
        /** @var TokenStorageDecorator $tokenStorage */
        $tokenStorage = self::$container->get('security.token_storage');

        $tokenStorage->setToken(null);
    }

    protected function getUser(string $role = 'ROLE_USER', bool $userFromDatabase = false): User
    {
        if (empty(self::$users[$role])) {
            self::$users[$role] = $userFromDatabase
                ? ($this->getFirstUserByRole($role) ?: $this->createNewUser($role, $userFromDatabase))
                : $this->createNewUser($role, $userFromDatabase);
        }

        return self::$users[$role];
    }

    protected function getFirstUserByRole(string $role = 'ROLE_USER'): ?User
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = self::$container->get('doctrine.orm.default_entity_manager');

        return $entityManager->getRepository(User::class)
            ->createQueryBuilder('u')
            ->where('u.roles LIKE :role')
            ->setParameter(':role', '%'.$role.'%')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    protected function createNewUser(string $role = 'ROLE_USE', bool $persist = false): User
    {
        $user = (new User())
            ->setRoles([$role])
            ->setEmail(sprintf('test_%s@test.com', strtolower($role)))
            ->setPassword('test')
            ->setFirstName('Test')
            ->setLastName('Test');

        if ($persist) {
            /** @var EntityManagerInterface $entityManager */
            $entityManager = self::$container->get('doctrine.orm.default_entity_manager');

            $entityManager->persist($user);
            $entityManager->flush();
        }

        return $user;
    }
}
