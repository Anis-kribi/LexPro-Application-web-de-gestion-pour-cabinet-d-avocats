<?php

namespace App\Controller;

use App\Repository\ClientRepository;
use App\Repository\DossierRepository;
use App\Repository\DocumentRepository;
use App\Repository\TacheRepository;
use App\Service\VisibilityService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GlobalSearchController extends AbstractController
{
    public function __construct(private readonly VisibilityService $visibilityService) {}

    #[Route('/backend/search', name: 'app_global_search', methods: ['GET'])]
    public function search(
        Request $request,
        ClientRepository $clientRepository,
        DossierRepository $dossierRepository,
        DocumentRepository $documentRepository,
        TacheRepository $tacheRepository
    ): Response {
        $q = trim((string) $request->query->get('q', ''));
        
        $results = [
            'clients' => [],
            'dossiers' => [],
            'documents' => [],
            'taches' => []
        ];

        /** @var \App\Entity\User $currentUser */
        $currentUser = $this->getUser();
        $effectiveAvocat = null;
        
        if ($currentUser && !$this->isGranted('ROLE_ADMIN')) {
            $effectiveAvocat = $this->visibilityService->getEffectiveAvocat($currentUser);
        }

        if (!empty($q)) {
            // Clients
            $qbC = $clientRepository->createQueryBuilder('c');
            $qbC->where('c.nom LIKE :q OR c.prenom LIKE :q OR c.nomEntreprise LIKE :q')
                ->setParameter('q', '%' . $q . '%');
            if ($effectiveAvocat) {
                $qbC->andWhere('c.avocat = :avocat')->setParameter('avocat', $effectiveAvocat);
            }
            $results['clients'] = $qbC->setMaxResults(10)->getQuery()->getResult();

            // Dossiers
            $qbD = $dossierRepository->createQueryBuilder('d');
            $qbD->where('d.titre LIKE :q OR d.numeroReference LIKE :q')
                ->setParameter('q', '%' . $q . '%');
            if ($effectiveAvocat) {
                $qbD->andWhere('d.avocat = :avocat')->setParameter('avocat', $effectiveAvocat);
            }
            $results['dossiers'] = $qbD->setMaxResults(10)->getQuery()->getResult();

            // Documents
            $qbDoc = $documentRepository->createQueryBuilder('doc');
            $qbDoc->join('doc.dossier', 'd')
                  ->where('doc.titre LIKE :q OR doc.nomOriginal LIKE :q')
                  ->setParameter('q', '%' . $q . '%');
            if ($effectiveAvocat) {
                $qbDoc->andWhere('d.avocat = :avocat')->setParameter('avocat', $effectiveAvocat);
            }
            $results['documents'] = $qbDoc->setMaxResults(10)->getQuery()->getResult();

            // Taches
            $qbT = $tacheRepository->createQueryBuilder('t');
            $qbT->join('t.dossier', 'd')
                ->where('t.titre LIKE :q')
                ->setParameter('q', '%' . $q . '%');
            if ($effectiveAvocat) {
                $qbT->andWhere('d.avocat = :avocat')->setParameter('avocat', $effectiveAvocat);
            }
            $results['taches'] = $qbT->setMaxResults(10)->getQuery()->getResult();
        }

        return $this->render('search/index.html.twig', [
            'q' => $q,
            'results' => $results,
        ]);
    }
}
