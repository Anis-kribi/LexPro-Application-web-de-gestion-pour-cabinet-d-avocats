<?php

namespace App\Controller\Backend;

use App\Repository\ClientRepository;
use App\Repository\DossierRepository;
use App\Repository\EntreeDeTempsRepository;
use App\Repository\FacturesRepository;
use App\Repository\TacheRepository;
use App\Service\VisibilityService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    public function __construct(private readonly VisibilityService $visibilityService) {}

    #[Route('/backend/dashboard', name: 'backend_dashboard')]
    public function index(
        ClientRepository        $clientRepository,
        DossierRepository       $dossierRepository,
        FacturesRepository      $facturesRepository,
        TacheRepository         $tacheRepository,
        EntreeDeTempsRepository $entreeDeTempsRepository
    ): Response {
        /** @var \App\Entity\User $currentUser */
        $currentUser     = $this->getUser();
        $effectiveAvocat = $this->visibilityService->getEffectiveAvocat($currentUser);

        // --- KPIs Clients ---
        $clientCount   = $effectiveAvocat === null
            ? $clientRepository->count([])
            : count($clientRepository->findVisibleByAvocat($effectiveAvocat));
        $clientsActifs = $clientRepository->countActifsForAvocat($effectiveAvocat);

        // --- KPIs Dossiers ---
        $statsDossiers   = $dossierRepository->countByStatutForAvocat($effectiveAvocat);
        $dossiersEnCours = $statsDossiers['En cours'] ?? 0;
        $totalDossiers   = array_sum($statsDossiers);

        // --- KPIs Factures ---
        $debutMois    = new \DateTime('first day of this month 00:00:00');
        $finMois      = new \DateTime('last day of this month 23:59:59');
        $facturesMois = $facturesRepository->findDuMoisCourantForAvocat($effectiveAvocat);
        $totalTtcMois = $facturesRepository->getTotalTtcPeriodeForAvocat($effectiveAvocat, $debutMois, $finMois);
        $facEnRetard  = count($facturesRepository->findEnRetardForAvocat($effectiveAvocat));

        // --- KPIs Tâches ---
        $statsTaches    = $tacheRepository->countByStatutForAvocat($effectiveAvocat);
        $tachesAFaire   = $statsTaches['À faire'] ?? 0;
        $tachesEnRetard = count($tacheRepository->findEnRetardForAvocat($effectiveAvocat));

        // --- Heures facturables du mois ---
        $heuresFactMois = $entreeDeTempsRepository->getTotalHeuresFacturablesMoisCourant();

        // --- Données graphique (6 derniers mois) ---
        $chartLabels   = [];
        $chartDossiers = [];
        $chartFactures = [];

        for ($i = 5; $i >= 0; $i--) {
            $date  = new \DateTime("first day of -$i months");
            $fin   = new \DateTime("last day of -$i months 23:59:59");
            $label = $date->format('M Y');
            $chartLabels[]   = $label;
            $chartDossiers[] = $totalDossiers; // simplifié
            $chartFactures[] = round($facturesRepository->getTotalTtcPeriodeForAvocat($effectiveAvocat, $date, $fin));
        }

        return $this->render('backend/Dashboard/dashboard.html.twig', [
            // Clients
            'client_count'        => $clientCount,
            'clients_actifs'      => $clientsActifs,
            // Dossiers
            'dossiers_en_cours'   => $dossiersEnCours,
            'total_dossiers'      => $totalDossiers,
            'stats_dossiers'      => $statsDossiers,
            // Factures
            'factures_mois'       => count($facturesMois),
            'total_ttc_mois'      => $totalTtcMois,
            'factures_en_retard'  => $facEnRetard,
            // Tâches
            'taches_a_faire'      => $tachesAFaire,
            'taches_en_retard'    => $tachesEnRetard,
            'stats_taches'        => $statsTaches,
            // Temps
            'heures_fact_mois'    => $heuresFactMois,
            // Graphiques
            'chart_labels'        => json_encode($chartLabels),
            'chart_dossiers'      => json_encode($chartDossiers),
            'chart_factures'      => json_encode($chartFactures),
            // Listes récentes (filtrées)
            'recent_dossiers'     => $dossierRepository->findRecentsForAvocat($effectiveAvocat, 5),
            'taches_urgentes'     => $tacheRepository->findUrgentesForAvocat($effectiveAvocat),
        ]);
    }
}
