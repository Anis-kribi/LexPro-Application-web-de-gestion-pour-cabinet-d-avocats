<?php

namespace App\Repository;

use App\Entity\Document;
use App\Entity\Dossier;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Document>
 */
class DocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Document::class);
    }

    /** Documents d'un dossier */
    public function findByDossier(Dossier $dossier): array
    {
        return $this->createQueryBuilder('doc')
            ->andWhere('doc.dossier = :dossier')
            ->setParameter('dossier', $dossier)
            ->leftJoin('doc.telechargepar', 'u')
            ->addSelect('u')
            ->orderBy('doc.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /** Documents par type */
    public function findByType(string $type): array
    {
        return $this->createQueryBuilder('doc')
            ->andWhere('doc.type = :type')
            ->setParameter('type', $type)
            ->orderBy('doc.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /** Recherche dans les documents (titre) */
    public function findByKeyword(string $keyword): array
    {
        return $this->createQueryBuilder('doc')
            ->andWhere('doc.titre LIKE :kw OR doc.nomOriginal LIKE :kw')
            ->setParameter('kw', '%' . $keyword . '%')
            ->orderBy('doc.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /** Documents récents */
    public function findRecents(int $limit = 5): array
    {
        return $this->createQueryBuilder('doc')
            ->leftJoin('doc.dossier', 'd')
            ->addSelect('d')
            ->leftJoin('doc.telechargepar', 'u')
            ->addSelect('u')
            ->orderBy('doc.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    // =========================================================================
    // MÉTHODES FILTRÉES PAR RÔLE
    // =========================================================================

    /**
     * Retourne les documents visibles par l'avocat effectif.
     * Un document est visible si son dossier est assigné à cet avocat.
     * @param User|null $effectiveAvocat null = pas de filtre (admin)
     */
    public function findVisibleByAvocat(?User $effectiveAvocat): array
    {
        $qb = $this->createQueryBuilder('doc')
            ->leftJoin('doc.dossier', 'd')
            ->addSelect('d')
            ->leftJoin('doc.telechargepar', 'u')
            ->addSelect('u')
            ->orderBy('doc.createdAt', 'DESC');

        if ($effectiveAvocat !== null) {
            $qb->andWhere('d.avocat = :avocat')
               ->setParameter('avocat', $effectiveAvocat);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Recherche par mot-clé filtrée par avocat
     * @param User|null $effectiveAvocat null = pas de filtre (admin)
     */
    public function findByKeywordForAvocat(?User $effectiveAvocat, string $keyword): array
    {
        $qb = $this->createQueryBuilder('doc')
            ->leftJoin('doc.dossier', 'd')
            ->addSelect('d')
            ->andWhere('doc.titre LIKE :kw OR doc.nomOriginal LIKE :kw')
            ->setParameter('kw', '%' . $keyword . '%')
            ->orderBy('doc.createdAt', 'DESC');

        if ($effectiveAvocat !== null) {
            $qb->andWhere('d.avocat = :avocat')
               ->setParameter('avocat', $effectiveAvocat);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Filtre par type + avocat
     * @param User|null $effectiveAvocat null = pas de filtre (admin)
     */
    public function findByTypeForAvocat(?User $effectiveAvocat, string $type): array
    {
        $qb = $this->createQueryBuilder('doc')
            ->leftJoin('doc.dossier', 'd')
            ->addSelect('d')
            ->andWhere('doc.type = :type')
            ->setParameter('type', $type)
            ->orderBy('doc.createdAt', 'DESC');

        if ($effectiveAvocat !== null) {
            $qb->andWhere('d.avocat = :avocat')
               ->setParameter('avocat', $effectiveAvocat);
        }

        return $qb->getQuery()->getResult();
    }
}
