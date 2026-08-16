<?php

namespace App\Repository;

use App\Entity\ArticleFacture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ArticleFacture>
 */
class ArticleFactureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ArticleFacture::class);
    }

    /** Articles d'une facture donnée */
    public function findByFacture(int $factureId): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.facture = :factureId')
            ->setParameter('factureId', $factureId)
            ->getQuery()
            ->getResult();
    }

    /** Total HT cumulé de tous les articles d'une facture */
    public function getTotalParFacture(int $factureId): float
    {
        $result = $this->createQueryBuilder('a')
            ->select('SUM(a.total)')
            ->andWhere('a.facture = :factureId')
            ->setParameter('factureId', $factureId)
            ->getQuery()
            ->getSingleScalarResult();

        return (float) ($result ?? 0);
    }
}
