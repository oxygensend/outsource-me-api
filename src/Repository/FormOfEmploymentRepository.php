<?php

namespace App\Repository;

use App\Entity\FormOfEmployment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FormOfEmployment>
 *
 * @method FormOfEmployment|null find($id, $lockMode = null, $lockVersion = null)
 * @method FormOfEmployment|null findOneBy(array $criteria, array $orderBy = null)
 * @method FormOfEmployment[]    findAll()
 * @method FormOfEmployment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormOfEmploymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FormOfEmployment::class);
    }

    public function add(FormOfEmployment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FormOfEmployment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return FormOfEmployment[] Returns an array of FormOfEmployment objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?FormOfEmployment
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
