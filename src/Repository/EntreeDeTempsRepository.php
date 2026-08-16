<?php

namespace App\Repository;

use App\Entity\EntreeDeTemps;
use App\Entity\Dossier;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EntreeDeTemps>
 */
class EntreeDeTempsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EntreeDeTemps::class);
    }

    /** Entrées de temps d'un dossier */
    public function findByDossier(Dossier $dossier): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.dossier = :dossier')
            ->setParameter('dossier', $dossier)
            ->leftJoin('e.user', 'u')
            ->addSelect('u')
            ->orderBy('e.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /** Entrées de temps par utilisateur */
    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.user = :user')
            ->setParameter('user', $user)
            ->leftJoin('e.dossier', 'd')
            ->addSelect('d')
            ->orderBy('e.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /** Heures facturables totales pour un dossier */
    public function getTotalHeuresFacturables(Dossier $dossier): float
    {
        $result = $this->createQueryBuilder('e')
            ->select('SUM(e.heures)')
            ->andWhere('e.dossier = :dossier')
            ->andWhere('e.facturable = true')
            ->setParameter('dossier', $dossier)
            ->getQuery()
            ->getSingleScalarResult();

        return (float) ($result ?? 0);
    }

    /** Heures totales pour un dossier (facturables + non) */
    public function getTotalHeures(Dossier $dossier): float
    {
        $result = $this->createQueryBuilder('e')
            ->select('SUM(e.heures)')
            ->andWhere('e.dossier = :dossier')
            ->setParameter('dossier', $dossier)
            ->getQuery()
            ->getSingleScalarResult();

        return (float) ($result ?? 0);
    }

    /** Entrées du mois courant par utilisateur */
    public function findDuMoisParUser(User $user): array
    {
        $debut = new \DateTime('first day of this month 00:00:00');
        $fin   = new \DateTime('last day of this month 23:59:59');

        return $this->createQueryBuilder('e')
            ->andWhere('e.user = :user')
            ->andWhere('e.date >= :debut')
            ->andWhere('e.date <= :fin')
            ->setParameter('user', $user)
            ->setParameter('debut', $debut)
            ->setParameter('fin', $fin)
            ->orderBy('e.date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /** Heures totales facturables du mois courant (pour dashboard) */
    public function getTotalHeuresFacturablesMoisCourant(): float
    {
        $debut = new \DateTime('first day of this month 00:00:00');
        $fin   = new \DateTime('last day of this month 23:59:59');

        $result = $this->createQueryBuilder('e')
            ->select('SUM(e.heures)')
            ->andWhere('e.facturable = true')
            ->andWhere('e.date >= :debut')
            ->andWhere('e.date <= :fin')
            ->setParameter('debut', $debut)
            ->setParameter('fin', $fin)
            ->getQuery()
            ->getSingleScalarResult();

        return (float) ($result ?? 0);
    }
}
