<?php

namespace App\Repository;

use App\Entity\Dossier;
use App\Entity\Client;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Dossier>
 */
class DossierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dossier::class);
    }

    /** Tous les dossiers avec client chargé (évite N+1) */
    public function findAllWithClient(): array
    {
        return $this->createQueryBuilder('d')
            ->leftJoin('d.client', 'c')
            ->addSelect('c')
            ->leftJoin('d.avocat', 'u')
            ->addSelect('u')
            ->orderBy('d.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /** Dossiers par client */
    public function findByClient(Client $client): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.client = :client')
            ->setParameter('client', $client)
            ->orderBy('d.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /** Dossiers par avocat */
    public function findByAvocat(User $avocat): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.avocat = :avocat')
            ->setParameter('avocat', $avocat)
            ->orderBy('d.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /** Dossiers par statut */
    public function findByStatut(string $statut): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.statuts = :statut')
            ->setParameter('statut', $statut)
            ->orderBy('d.id', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /** Dossiers en cours uniquement */
    public function findEnCours(): array
    {
        return $this->findByStatut('En cours');
    }

    /** Compte des dossiers par statut */
    public function countByStatut(): array
    {
        $result = $this->createQueryBuilder('d')
            ->select('d.statuts, COUNT(d.id) as total')
            ->groupBy('d.statuts')
            ->getQuery()
            ->getResult();

        $counts = [];
        foreach ($result as $row) {
            $counts[$row['statuts']] = (int) $row['total'];
        }
        return $counts;
    }

    /** Dossiers récents */
    public function findRecents(int $limit = 5): array
    {
        return $this->createQueryBuilder('d')
            ->leftJoin('d.client', 'c')
            ->addSelect('c')
            ->orderBy('d.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche + filtres combinés
     */
    public function findWithFilters(
        ?string $keyword = null,
        ?string $statut = null,
        ?string $priorite = null,
        ?int $clientId = null,
        int $page = 1,
        int $limit = 15
    ): array {
        $qb = $this->createQueryBuilder('d')
            ->leftJoin('d.client', 'c')
            ->addSelect('c')
            ->leftJoin('d.avocat', 'u')
            ->addSelect('u');

        if ($keyword) {
            $qb->andWhere('d.titre LIKE :kw OR d.numeroReference LIKE :kw OR c.nom LIKE :kw')
               ->setParameter('kw', '%' . $keyword . '%');
        }

        if ($statut) {
            $qb->andWhere('d.statuts = :statut')->setParameter('statut', $statut);
        }

        if ($priorite) {
            $qb->andWhere('d.priorite = :priorite')->setParameter('priorite', $priorite);
        }

        if ($clientId) {
            $qb->andWhere('d.client = :clientId')->setParameter('clientId', $clientId);
        }

        $qb->orderBy('d.id', 'DESC');

        $total = count($qb->getQuery()->getResult());

        $qb->setFirstResult(($page - 1) * $limit)->setMaxResults($limit);

        return [
            'data'  => $qb->getQuery()->getResult(),
            'total' => $total,
        ];
    }

    // =========================================================================
    // MÉTHODES FILTRÉES PAR RÔLE
    // $effectiveAvocat = null → admin (aucun filtre)
    // $effectiveAvocat = User → filtre sur cet avocat
    // =========================================================================

    /**
     * Retourne tous les dossiers visibles par l'avocat effectif.
     * @param User|null $effectiveAvocat null = pas de filtre (admin)
     */
    public function findVisibleByAvocat(?User $effectiveAvocat): array
    {
        $qb = $this->createQueryBuilder('d')
            ->leftJoin('d.client', 'c')
            ->addSelect('c')
            ->leftJoin('d.avocat', 'u')
            ->addSelect('u')
            ->orderBy('d.id', 'DESC');

        if ($effectiveAvocat !== null) {
            $qb->andWhere('d.avocat = :avocat')
               ->setParameter('avocat', $effectiveAvocat);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * findWithFilters + filtre avocat
     * @param User|null $effectiveAvocat null = pas de filtre (admin)
     */
    public function findWithFiltersForAvocat(
        ?User $effectiveAvocat,
        ?string $keyword = null,
        ?string $statut = null,
        ?string $priorite = null,
        ?int $clientId = null,
        int $page = 1,
        int $limit = 15
    ): array {
        $qb = $this->createQueryBuilder('d')
            ->leftJoin('d.client', 'c')
            ->addSelect('c')
            ->leftJoin('d.avocat', 'u')
            ->addSelect('u');

        if ($effectiveAvocat !== null) {
            $qb->andWhere('d.avocat = :avocat')
               ->setParameter('avocat', $effectiveAvocat);
        }

        if ($keyword) {
            $qb->andWhere('d.titre LIKE :kw OR d.numeroReference LIKE :kw OR c.nom LIKE :kw')
               ->setParameter('kw', '%' . $keyword . '%');
        }

        if ($statut) {
            $qb->andWhere('d.statuts = :statut')->setParameter('statut', $statut);
        }

        if ($priorite) {
            $qb->andWhere('d.priorite = :priorite')->setParameter('priorite', $priorite);
        }

        if ($clientId) {
            $qb->andWhere('d.client = :clientId')->setParameter('clientId', $clientId);
        }

        $qb->orderBy('d.id', 'DESC');

        $total = count($qb->getQuery()->getResult());

        $qb->setFirstResult(($page - 1) * $limit)->setMaxResults($limit);

        return [
            'data'  => $qb->getQuery()->getResult(),
            'total' => $total,
        ];
    }

    /**
     * countByStatut filtré par avocat
     * @param User|null $effectiveAvocat null = pas de filtre (admin)
     */
    public function countByStatutForAvocat(?User $effectiveAvocat): array
    {
        $qb = $this->createQueryBuilder('d')
            ->select('d.statuts, COUNT(d.id) as total')
            ->groupBy('d.statuts');

        if ($effectiveAvocat !== null) {
            $qb->andWhere('d.avocat = :avocat')
               ->setParameter('avocat', $effectiveAvocat);
        }

        $result = $qb->getQuery()->getResult();

        $counts = [];
        foreach ($result as $row) {
            $counts[$row['statuts']] = (int) $row['total'];
        }
        return $counts;
    }

    /**
     * Dossiers récents filtrés par avocat
     * @param User|null $effectiveAvocat null = pas de filtre (admin)
     */
    public function findRecentsForAvocat(?User $effectiveAvocat, int $limit = 5): array
    {
        $qb = $this->createQueryBuilder('d')
            ->leftJoin('d.client', 'c')
            ->addSelect('c')
            ->orderBy('d.id', 'DESC')
            ->setMaxResults($limit);

        if ($effectiveAvocat !== null) {
            $qb->andWhere('d.avocat = :avocat')
               ->setParameter('avocat', $effectiveAvocat);
        }

        return $qb->getQuery()->getResult();
    }
}
