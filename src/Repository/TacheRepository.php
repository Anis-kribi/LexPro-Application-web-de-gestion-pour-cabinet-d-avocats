<?php

namespace App\Repository;

use App\Entity\Tache;
use App\Entity\Dossier;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tache>
 */
class TacheRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tache::class);
    }

    /** Tâches d'un dossier */
    public function findByDossier(Dossier $dossier): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.dossier = :dossier')
            ->setParameter('dossier', $dossier)
            ->leftJoin('t.assigneA', 'u')
            ->addSelect('u')
            ->orderBy('t.dateEcheance', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** Tâches assignées à un utilisateur */
    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.assigneA = :user')
            ->setParameter('user', $user)
            ->leftJoin('t.dossier', 'd')
            ->addSelect('d')
            ->orderBy('t.dateEcheance', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** Tâches en retard (date dépassée, non terminées) */
    public function findEnRetard(): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.dateEcheance < :today')
            ->andWhere('t.status != :done')
            ->setParameter('today', new \DateTime('today'))
            ->setParameter('done', 'Terminée')
            ->orderBy('t.dateEcheance', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** Tâches par statut */
    public function findByStatut(string $status): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.status = :status')
            ->setParameter('status', $status)
            ->orderBy('t.dateEcheance', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** Tâches urgentes à faire (priorité Haute ou Urgente, non terminées) */
    public function findUrgentes(int $limit = 5): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.priorite IN (:prios)')
            ->andWhere('t.status != :done')
            ->setParameter('prios', ['Haute', 'Urgente'])
            ->setParameter('done', 'Terminée')
            ->leftJoin('t.dossier', 'd')
            ->addSelect('d')
            ->orderBy('t.dateEcheance', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /** Statistiques par statut */
    public function countByStatut(): array
    {
        $result = $this->createQueryBuilder('t')
            ->select('t.status, COUNT(t.id) as total')
            ->groupBy('t.status')
            ->getQuery()
            ->getResult();

        $counts = [];
        foreach ($result as $row) {
            $counts[$row['status']] = (int) $row['total'];
        }
        return $counts;
    }

    // =========================================================================
    // MÉTHODES FILTRÉES PAR RÔLE
    // =========================================================================

    /**
     * Toutes les tâches visibles par l'avocat effectif.
     * Une tâche est visible si son dossier est assigné à cet avocat.
     * @param User|null $effectiveAvocat null = pas de filtre (admin)
     */
    public function findVisibleByAvocat(?User $effectiveAvocat): array
    {
        $qb = $this->createQueryBuilder('t')
            ->leftJoin('t.dossier', 'd')
            ->addSelect('d')
            ->leftJoin('t.assigneA', 'u')
            ->addSelect('u')
            ->orderBy('t.dateEcheance', 'ASC');

        if ($effectiveAvocat !== null) {
            $qb->andWhere('d.avocat = :avocat')
               ->setParameter('avocat', $effectiveAvocat);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Tâches par statut filtrées par avocat
     * @param User|null $effectiveAvocat null = pas de filtre (admin)
     */
    public function findByStatutForAvocat(?User $effectiveAvocat, string $status): array
    {
        $qb = $this->createQueryBuilder('t')
            ->leftJoin('t.dossier', 'd')
            ->addSelect('d')
            ->andWhere('t.status = :status')
            ->setParameter('status', $status)
            ->orderBy('t.dateEcheance', 'ASC');

        if ($effectiveAvocat !== null) {
            $qb->andWhere('d.avocat = :avocat')
               ->setParameter('avocat', $effectiveAvocat);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * countByStatut filtré par avocat
     * @param User|null $effectiveAvocat null = pas de filtre (admin)
     */
    public function countByStatutForAvocat(?User $effectiveAvocat): array
    {
        $qb = $this->createQueryBuilder('t')
            ->select('t.status, COUNT(t.id) as total')
            ->leftJoin('t.dossier', 'd')
            ->groupBy('t.status');

        if ($effectiveAvocat !== null) {
            $qb->andWhere('d.avocat = :avocat')
               ->setParameter('avocat', $effectiveAvocat);
        }

        $result = $qb->getQuery()->getResult();

        $counts = [];
        foreach ($result as $row) {
            $counts[$row['status']] = (int) $row['total'];
        }
        return $counts;
    }

    /**
     * Tâches urgentes filtrées par avocat
     * @param User|null $effectiveAvocat null = pas de filtre (admin)
     */
    public function findUrgentesForAvocat(?User $effectiveAvocat, int $limit = 5): array
    {
        $qb = $this->createQueryBuilder('t')
            ->andWhere('t.priorite IN (:prios)')
            ->andWhere('t.status != :done')
            ->setParameter('prios', ['Haute', 'Urgente'])
            ->setParameter('done', 'Terminée')
            ->leftJoin('t.dossier', 'd')
            ->addSelect('d')
            ->orderBy('t.dateEcheance', 'ASC')
            ->setMaxResults($limit);

        if ($effectiveAvocat !== null) {
            $qb->andWhere('d.avocat = :avocat')
               ->setParameter('avocat', $effectiveAvocat);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Tâches en retard filtrées par avocat
     * @param User|null $effectiveAvocat null = pas de filtre (admin)
     */
    public function findEnRetardForAvocat(?User $effectiveAvocat): array
    {
        $qb = $this->createQueryBuilder('t')
            ->andWhere('t.dateEcheance < :today')
            ->andWhere('t.status != :done')
            ->setParameter('today', new \DateTime('today'))
            ->setParameter('done', 'Terminée')
            ->leftJoin('t.dossier', 'd')
            ->addSelect('d')
            ->orderBy('t.dateEcheance', 'ASC');

        if ($effectiveAvocat !== null) {
            $qb->andWhere('d.avocat = :avocat')
               ->setParameter('avocat', $effectiveAvocat);
        }

        return $qb->getQuery()->getResult();
    }
}
