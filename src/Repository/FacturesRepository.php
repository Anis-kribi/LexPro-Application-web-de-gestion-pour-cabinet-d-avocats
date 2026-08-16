<?php

namespace App\Repository;

use App\Entity\Factures;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Factures>
 */
class FacturesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Factures::class);
    }

    /** Toutes les factures avec client et dossier chargés */
    public function findAllWithRelations(): array
    {
        return $this->createQueryBuilder('f')
            ->leftJoin('f.client', 'c')
            ->addSelect('c')
            ->leftJoin('f.dossier', 'd')
            ->addSelect('d')
            ->orderBy('f.dateEmission', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /** Factures par client */
    public function findByClient(int $clientId): array
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.client = :clientId')
            ->setParameter('clientId', $clientId)
            ->orderBy('f.dateEmission', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /** Factures par dossier */
    public function findByDossier(int $dossierId): array
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.dossier = :dossierId')
            ->setParameter('dossierId', $dossierId)
            ->orderBy('f.dateEmission', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /** Factures par statut */
    public function findByStatut(string $statut): array
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.status = :statut')
            ->setParameter('statut', $statut)
            ->orderBy('f.dateEmission', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /** Total TTC sur une période (pour dashboard) */
    public function getTotalTtcPeriode(\DateTimeInterface $debut, \DateTimeInterface $fin): float
    {
        $result = $this->createQueryBuilder('f')
            ->select('SUM(f.montantTtc)')
            ->andWhere('f.dateEmission >= :debut')
            ->andWhere('f.dateEmission <= :fin')
            ->andWhere('f.status != :annulee')
            ->setParameter('debut', $debut)
            ->setParameter('fin', $fin)
            ->setParameter('annulee', 'Annulée')
            ->getQuery()
            ->getSingleScalarResult();

        return (float) ($result ?? 0);
    }

    /** Factures du mois courant */
    public function findDuMoisCourant(): array
    {
        $debut = new \DateTime('first day of this month 00:00:00');
        $fin   = new \DateTime('last day of this month 23:59:59');

        return $this->createQueryBuilder('f')
            ->andWhere('f.dateEmission >= :debut')
            ->andWhere('f.dateEmission <= :fin')
            ->setParameter('debut', $debut)
            ->setParameter('fin', $fin)
            ->orderBy('f.dateEmission', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /** Factures en retard (status = "En attente" et dateEcheance dépassée) */
    public function findEnRetard(): array
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.status = :status')
            ->andWhere('f.dateEcheance < :today')
            ->setParameter('status', 'En attente')
            ->setParameter('today', new \DateTime('today'))
            ->orderBy('f.dateEcheance', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** Statistiques par statut */
    public function getStatsByStatut(): array
    {
        $result = $this->createQueryBuilder('f')
            ->select('f.status, COUNT(f.id) as nb, SUM(f.montantTtc) as total')
            ->groupBy('f.status')
            ->getQuery()
            ->getResult();

        return $result;
    }

    // =========================================================================
    // MÉTHODES FILTRÉES PAR RÔLE
    // =========================================================================

    /**
     * Toutes les factures visibles par l'avocat effectif.
     * Une facture est visible si son dossier est assigné à cet avocat.
     * @param User|null $effectiveAvocat null = pas de filtre (admin)
     */
    public function findVisibleByAvocat(?User $effectiveAvocat): array
    {
        $qb = $this->createQueryBuilder('f')
            ->leftJoin('f.client', 'c')
            ->addSelect('c')
            ->leftJoin('f.dossier', 'd')
            ->addSelect('d')
            ->orderBy('f.dateEmission', 'DESC');

        if ($effectiveAvocat !== null) {
            $qb->andWhere('d.avocat = :avocat')
               ->setParameter('avocat', $effectiveAvocat);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Factures par statut filtrées par avocat
     * @param User|null $effectiveAvocat null = pas de filtre (admin)
     */
    public function findByStatutForAvocat(?User $effectiveAvocat, string $statut): array
    {
        $qb = $this->createQueryBuilder('f')
            ->leftJoin('f.dossier', 'd')
            ->addSelect('d')
            ->andWhere('f.status = :statut')
            ->setParameter('statut', $statut)
            ->orderBy('f.dateEmission', 'DESC');

        if ($effectiveAvocat !== null) {
            $qb->andWhere('d.avocat = :avocat')
               ->setParameter('avocat', $effectiveAvocat);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Statistiques par statut filtrées par avocat
     * @param User|null $effectiveAvocat null = pas de filtre (admin)
     */
    public function getStatsByStatutForAvocat(?User $effectiveAvocat): array
    {
        $qb = $this->createQueryBuilder('f')
            ->select('f.status, COUNT(f.id) as nb, SUM(f.montantTtc) as total')
            ->leftJoin('f.dossier', 'd')
            ->groupBy('f.status');

        if ($effectiveAvocat !== null) {
            $qb->andWhere('d.avocat = :avocat')
               ->setParameter('avocat', $effectiveAvocat);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Total TTC sur une période filtré par avocat
     * @param User|null $effectiveAvocat null = pas de filtre (admin)
     */
    public function getTotalTtcPeriodeForAvocat(?User $effectiveAvocat, \DateTimeInterface $debut, \DateTimeInterface $fin): float
    {
        $qb = $this->createQueryBuilder('f')
            ->select('SUM(f.montantTtc)')
            ->leftJoin('f.dossier', 'd')
            ->andWhere('f.dateEmission >= :debut')
            ->andWhere('f.dateEmission <= :fin')
            ->andWhere('f.status != :annulee')
            ->setParameter('debut', $debut)
            ->setParameter('fin', $fin)
            ->setParameter('annulee', 'Annulée');

        if ($effectiveAvocat !== null) {
            $qb->andWhere('d.avocat = :avocat')
               ->setParameter('avocat', $effectiveAvocat);
        }

        return (float) ($qb->getQuery()->getSingleScalarResult() ?? 0);
    }

    /**
     * Factures du mois courant filtrées par avocat
     * @param User|null $effectiveAvocat null = pas de filtre (admin)
     */
    public function findDuMoisCourantForAvocat(?User $effectiveAvocat): array
    {
        $debut = new \DateTime('first day of this month 00:00:00');
        $fin   = new \DateTime('last day of this month 23:59:59');

        $qb = $this->createQueryBuilder('f')
            ->leftJoin('f.dossier', 'd')
            ->addSelect('d')
            ->andWhere('f.dateEmission >= :debut')
            ->andWhere('f.dateEmission <= :fin')
            ->setParameter('debut', $debut)
            ->setParameter('fin', $fin)
            ->orderBy('f.dateEmission', 'DESC');

        if ($effectiveAvocat !== null) {
            $qb->andWhere('d.avocat = :avocat')
               ->setParameter('avocat', $effectiveAvocat);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Factures en retard filtrées par avocat
     * @param User|null $effectiveAvocat null = pas de filtre (admin)
     */
    public function findEnRetardForAvocat(?User $effectiveAvocat): array
    {
        $qb = $this->createQueryBuilder('f')
            ->leftJoin('f.dossier', 'd')
            ->addSelect('d')
            ->andWhere('f.status = :status')
            ->andWhere('f.dateEcheance < :today')
            ->setParameter('status', 'En attente')
            ->setParameter('today', new \DateTime('today'))
            ->orderBy('f.dateEcheance', 'ASC');

        if ($effectiveAvocat !== null) {
            $qb->andWhere('d.avocat = :avocat')
               ->setParameter('avocat', $effectiveAvocat);
        }

        return $qb->getQuery()->getResult();
    }
}
