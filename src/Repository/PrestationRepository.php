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

    public function getFilteredQueryBuilder(array $filters): array
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.client', 'c')->addSelect('c')
            ->leftJoin('p.employee', 'e')->addSelect('e')
            ->leftJoin('p.service', 's')->addSelect('s')
            ->orderBy('p.id','DESC');

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
            $annee = (new \DateTime())->format('Y'); // année actuelle
            $mois = str_pad($filters['mois'], 2, '0', STR_PAD_LEFT);
            $startDate = new \DateTime("$annee-$mois-01");
            $endDate = (clone $startDate)->modify('first day of next month');

            $qb->andWhere('p.date >= :start')
               ->andWhere('p.date < :end')
               ->setParameter('start', $startDate)
               ->setParameter('end', $endDate);
        }

        return $qb->getQuery()->getResult();
    }
    public function findByEmployee($employee)
    {
        return $this->createQueryBuilder('p')
            ->where('p.employee = :employee')
            ->setParameter('employee', $employee)
            ->getQuery()
            ->getResult();
    }

}