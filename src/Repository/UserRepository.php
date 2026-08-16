<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * Compte le nombre d'utilisateurs ayant ROLE_ADMIN.
     */
    public function countAdmins(): int
    {
        $results = $this->createQueryBuilder('u')
            ->select('u.roles')
            ->getQuery()
            ->getArrayResult();

        $count = 0;
        foreach ($results as $row) {
            if (in_array('ROLE_ADMIN', $row['roles'], true)) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Vérifie s'il existe au moins un admin autre que l'utilisateur donné.
     */
    public function hasOtherAdmin(int $excludeUserId): bool
    {
        $results = $this->createQueryBuilder('u')
            ->select('u.id, u.roles')
            ->where('u.id != :id')
            ->setParameter('id', $excludeUserId)
            ->getQuery()
            ->getArrayResult();

        foreach ($results as $row) {
            if (in_array('ROLE_ADMIN', $row['roles'], true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retourne tous les utilisateurs triés par date de création décroissante.
     *
     * @return User[]
     */
    public function findAllOrderedByDate(): array
    {
        return $this->createQueryBuilder('u')
            ->orderBy('u.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
