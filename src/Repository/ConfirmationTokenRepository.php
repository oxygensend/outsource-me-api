<?php

namespace App\Repository;

use App\Entity\ConfirmationToken;
use App\Entity\ConfirmationTokens;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ConfirmationToken>
 *
 * @method ConfirmationToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConfirmationToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConfirmationToken[]    findAll()
 * @method ConfirmationToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConfirmationTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConfirmationToken::class);
    }

    public function add(ConfirmationToken $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ConfirmationToken $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findValidUserConfirmationTokens(User $user, string $type): array
    {
        $now = new \DateTime();

        return $this->createQueryBuilder('ct')
            ->andWhere('ct.user = :user')
            ->andWhere('ct.type = :type')
            ->andWhere('ct.expiredAt >= :date')
            ->setParameter('user', $user)
            ->setParameter('type', $type)
            ->setParameter('date', $now)
            ->orderBy('ct.expiredAt', 'DESC')
            ->getQuery()
            ->getResult();

    }

    public function findValidConfirmationTokens(string $token, string $type): array
    {
        $now = new \DateTimeImmutable();

        return $this->createQueryBuilder('ct')
            ->andWhere('ct.token = :token')
            ->andWhere('ct.type = :type')
            ->andWhere('ct.expiredAt >= :date')
            ->setParameter('token', $token)
            ->setParameter('type', $type)
            ->setParameter('date', $now)
            ->orderBy('ct.expiredAt', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function removeUserTokensOfType(User $user, string $type): void
    {
        $this->_em->createQueryBuilder()
            ->delete()
            ->from(ConfirmationToken::class, 'ct')
            ->andWhere('ct.user = :user')
            ->andWhere('ct.type = :type')
            ->setParameter('user', $user)
            ->setParameter('type', $type)
            ->getQuery()
            ->execute()
        ;
    }

}