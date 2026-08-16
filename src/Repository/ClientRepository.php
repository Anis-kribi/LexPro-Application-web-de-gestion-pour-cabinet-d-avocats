<?php

namespace App\Repository;

use App\Entity\Client;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Client>
 */
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    /** Tous les clients, triés par nom */
    public function findAllClients(): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche par mot-clé (nom, prénom, email, téléphone, entreprise, ville)
     */
    public function findByKeyword(string $keyword): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.nom LIKE :kw OR c.prenom LIKE :kw OR c.email LIKE :kw OR c.telephone LIKE :kw OR c.nomEntreprise LIKE :kw OR c.ville LIKE :kw')
            ->setParameter('kw', '%' . $keyword . '%')
            ->orderBy('c.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** Filtre par statut */
    public function findByStatut(string $statut): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.statuts = :statut')
            ->setParameter('statut', $statut)
            ->orderBy('c.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** Filtre par type (particulier / entreprise) */
    public function findByType(string $type): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.type = :type')
            ->setParameter('type', $type)
            ->orderBy('c.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** Compte les clients actifs */
    public function countActifs(): int
    {
        return (int) $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->andWhere('c.statuts = :statut')
            ->setParameter('statut', 'Actif')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Recherche + filtre combinés avec pagination manuelle
     * @return array{data: Client[], total: int}
     */
    public function findWithFilters(
        ?string $keyword = null,
        ?string $statut = null,
        ?string $type = null,
        int $page = 1,
        int $limit = 15
    ): array {
        $qb = $this->createQueryBuilder('c');

        if ($keyword) {
            $qb->andWhere('c.nom LIKE :kw OR c.prenom LIKE :kw OR c.email LIKE :kw OR c.nomEntreprise LIKE :kw')
               ->setParameter('kw', '%' . $keyword . '%');
        }

        if ($statut) {
            $qb->andWhere('c.statuts = :statut')->setParameter('statut', $statut);
        }

        if ($type) {
            $qb->andWhere('c.type = :type')->setParameter('type', $type);
        }

        $qb->orderBy('c.createdAt', 'DESC');

        $paginator = new Paginator($qb);
        $total = count($paginator);

        $qb->setFirstResult(($page - 1) * $limit)->setMaxResults($limit);

        return [
            'data'  => $qb->getQuery()->getResult(),
            'total' => $total,
        ];
    }

    // =========================================================================
    // MÉTHODES FILTRÉES PAR RÔLE
    // La visibilité d'un client est dérivée des dossiers auxquels l'avocat est assigné.
    // =========================================================================

    /**
     * Retourne les clients visibles par l'avocat effectif.
     * Un client est visible s'il a le même avocat effectif que l'utilisateur,
     * OU s'il a au moins un dossier assigné à cet avocat.
     * @param User|null $effectiveAvocat null = pas de filtre (admin)
     */
    public function findVisibleByAvocat(?User $effectiveAvocat): array
    {
        $qb = $this->createQueryBuilder('c')
            ->orderBy('c.nom', 'ASC');

        if ($effectiveAvocat !== null) {
            $qb->leftJoin('c.dossiers', 'd')
               ->andWhere('c.avocat = :avocat OR d.avocat = :avocat')
               ->setParameter('avocat', $effectiveAvocat);
               // Removed distinct() as Doctrine handles objects usually,
               // but let's keep it to be safe for counts.
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * findWithFilters + filtre avocat
     * @param User|null $effectiveAvocat null = pas de filtre (admin)
     * @return array{data: Client[], total: int}
     */
    public function findWithFiltersForAvocat(
        ?User $effectiveAvocat,
        ?string $keyword = null,
        ?string $statut = null,
        ?string $type = null,
        int $page = 1,
        int $limit = 15
    ): array {
        $qb = $this->createQueryBuilder('c');

        if ($effectiveAvocat !== null) {
            $qb->leftJoin('c.dossiers', 'd')
               ->andWhere('c.avocat = :avocat OR d.avocat = :avocat')
               ->setParameter('avocat', $effectiveAvocat)
               ->distinct();
        }

        if ($keyword) {
            $qb->andWhere('c.nom LIKE :kw OR c.prenom LIKE :kw OR c.email LIKE :kw OR c.nomEntreprise LIKE :kw')
               ->setParameter('kw', '%' . $keyword . '%');
        }

        if ($statut) {
            $qb->andWhere('c.statuts = :statut')->setParameter('statut', $statut);
        }

        if ($type) {
            $qb->andWhere('c.type = :type')->setParameter('type', $type);
        }

        $qb->orderBy('c.createdAt', 'DESC');

        $total = count($qb->getQuery()->getResult());

        $qb->setFirstResult(($page - 1) * $limit)->setMaxResults($limit);

        return [
            'data'  => $qb->getQuery()->getResult(),
            'total' => $total,
        ];
    }

    /**
     * Compte les clients actifs filtrés par avocat
     * @param User|null $effectiveAvocat null = pas de filtre (admin)
     */
    public function countActifsForAvocat(?User $effectiveAvocat): int
    {
        $qb = $this->createQueryBuilder('c')
            ->select('COUNT(DISTINCT c.id)')
            ->andWhere('c.statuts = :statut')
            ->setParameter('statut', 'Actif');

        if ($effectiveAvocat !== null) {
            $qb->leftJoin('c.dossiers', 'd')
               ->andWhere('c.avocat = :avocat OR d.avocat = :avocat')
               ->setParameter('avocat', $effectiveAvocat);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
