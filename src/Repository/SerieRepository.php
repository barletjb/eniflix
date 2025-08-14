<?php

namespace App\Repository;

use App\Entity\Serie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Serie>
 */
class SerieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Serie::class);
    }

    public function findSeriesCustom(float $popularity, float $vote): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.popularity > :popularity')
            ->andWhere('s.vote < :vote')
            ->orderBy('s.popularity', 'DESC')
            ->addOrderBy('s.firstAirDate', 'DESC')
            ->setParameter('popularity', $popularity)
            ->setParameter('vote', $vote)
            ->setFirstResult(0)
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function findSeriesWithDQl(float $popularity, float $vote)
    {
        $dql = "SELECT s FROM App\Entity\Serie s 
                WHERE (s.popularity > :popularity OR s.firstAirDate > :date) AND s.vote > :vote 
                ORDER BY s.popularity DESC, s.firstAirDate DESC";
        return $this->getEntityManager()->createQuery($dql)
            ->setFirstResult(0)
            ->setMaxResults(10)
            ->setParameter(':popularity', $popularity)
            ->setParameter(':vote', $vote)
            ->setParameter(':date', new \DateTime('- 5 years'))
            ->execute();
    }

    //    /**
    //     * @return Serie[] Returns an array of Serie objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Serie
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
