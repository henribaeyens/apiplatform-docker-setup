<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use App\Entity\UserInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method UserInterface|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserInterface|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserInterface[]    findAll()
 * @method UserInterface[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
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

    public function save(UserInterface $user): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function findAdminByEmail(string $email): UserInterface|null
    {
        try {
            $user =
                $this->createQueryBuilder('u')
                    ->andWhere('u.email = :email')
                    ->andWhere('JSON_CONTAINS(u.roles, :role) = 1')
                    ->setParameters([
                        'email' => $email,
                        'role' => '"ROLE_ADMIN"',
                    ])
                    ->getQuery()
                    ->getOneOrNullResult()
            ;
        } catch (NonUniqueResultException $e) {
            return null;
        }

        return $user;
    }
}
