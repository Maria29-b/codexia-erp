<?php

namespace App\Repository;

use App\Entity\Prestation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Prestation>
 */
class PrestationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Prestation::class);
    }

    public function findByFilters(array $filters): array
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.client', 'c')
            ->leftJoin('p.employee', 'e')
            ->leftJoin('p.service', 's')
            ->addSelect('c', 'e', 's');

        if (!empty($filters['client'])) {
            $qb->andWhere('c.id = :client')
            ->setParameter('client', $filters['client']);
        }

        if (!empty($filters['employee'])) {
            $qb->andWhere('e.id = :employee')
            ->setParameter('employee', $filters['employee']);
        }

        if (!empty($filters['service'])) {
            $qb->andWhere('s.id = :service')
            ->setParameter('service', $filters['service']);
        }

        if (!empty($filters['statut'])) {
            $qb->andWhere('p.statut = :statut')
            ->setParameter('statut', $filters['statut']);
        }

        if (!empty($filters['mois'])) {
            $qb->andWhere('MONTH(p.date) = :mois')
            ->setParameter('mois', $filters['mois']);
        }

        return $qb->getQuery()->getResult();
    }

    //    /**
    //     * @return Prestation[] Returns an array of Prestation objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Prestation
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
