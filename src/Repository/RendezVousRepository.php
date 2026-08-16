<?php

namespace App\Repository;

use App\Entity\RendezVous;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RendezVous|null find($id, $lockMode = null, $lockVersion = null)
 * @method RendezVous|null findOneBy(array $criteria, array $orderBy = null)
 * @method RendezVous[]    findAll()
 * @method RendezVous[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RendezVousRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RendezVous::class);
    }

    /**
     * Get all appointments for a specific date.
     */
    public function findByDate(\DateTime $date)
    {
        return $this->createQueryBuilder('r')
            ->where('r.date >= :start_date')
            ->andWhere('r.date <= :end_date')
            ->setParameter('start_date', $date->setTime(0, 0))
            ->setParameter('end_date', $date->setTime(23, 59))
            ->getQuery()
            ->getResult();
    }

    /**
     * Get appointments for a specific client.
     */
    public function findByClient($clientId)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.client = :client')
            ->setParameter('client', $clientId)
            ->getQuery()
            ->getResult();
    }

    /**
     * Get all appointments for today.
     */
    public function findAppointmentsForToday()
    {
        $today = new \DateTime();
        return $this->createQueryBuilder('r')
            ->where('r.date >= :today')
            ->andWhere('r.date < :tomorrow')
            ->setParameter('today', $today->setTime(0, 0))
            ->setParameter('tomorrow', $today->setTime(23, 59))
            ->getQuery()
            ->getResult();
    }
}
