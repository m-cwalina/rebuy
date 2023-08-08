<?php

namespace App\Repository;

use App\Entity\EANCode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EANCode>
 *
 * @method EANCode|null find($id, $lockMode = null, $lockVersion = null)
 * @method EANCode|null findOneBy(array $criteria, array $orderBy = null)
 * @method EANCode[]    findAll()
 * @method EANCode[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EANCodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EANCode::class);
    }

//    /**
//     * @return EANCode[] Returns an array of EANCode objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?EANCode
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
