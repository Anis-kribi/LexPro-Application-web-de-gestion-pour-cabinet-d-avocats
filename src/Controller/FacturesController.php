<?php

namespace App\Controller;

use App\Entity\Factures;
use App\Form\FacturesType;
use App\Repository\FacturesRepository;
use App\Service\VisibilityService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/factures')]
final class FacturesController extends AbstractController
{
    public function __construct(private readonly VisibilityService $visibilityService) {}

    #[Route('/', name: 'app_factures_index', methods: ['GET'])]
    public function index(Request $request, FacturesRepository $facturesRepository): Response
    {
        /** @var \App\Entity\User $currentUser */
        $currentUser     = $this->getUser();
        $effectiveAvocat = $this->visibilityService->getEffectiveAvocat($currentUser);

        $statut   = $request->query->get('statut');
        $clientId = $request->query->get('client');

        if ($statut) {
            $factures = $facturesRepository->findByStatutForAvocat($effectiveAvocat, $statut);
        } else {
            $factures = $facturesRepository->findVisibleByAvocat($effectiveAvocat);
        }

        $stats     = $facturesRepository->getStatsByStatutForAvocat($effectiveAvocat);
        $debutMois = new \DateTime('first day of this month');
        $finMois   = new \DateTime('last day of this month');
        $totalMois = $facturesRepository->getTotalTtcPeriodeForAvocat($effectiveAvocat, $debutMois, $finMois);
        $enRetard  = count($facturesRepository->findEnRetardForAvocat($effectiveAvocat));

        return $this->render('factures/index.html.twig', [
            'factures'   => $factures,
            'stats'      => $stats,
            'totalMois'  => $totalMois,
            'enRetard'   => $enRetard,
            'statut'     => $statut,
        ]);
    }

    #[Route('/new', name: 'app_factures_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_AVOCAT');
        $facture = new Factures();
        $form    = $this->createForm(FacturesType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $facture->recalculerDepuisArticles();
            $em->persist($facture);
            $em->flush();
            $this->addFlash('success', 'Facture créée avec succès.');
            return $this->redirectToRoute('app_factures_show', ['id' => $facture->getId()]);
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('error', 'Veuillez corriger les erreurs du formulaire.');
        }

        return $this->render('factures/new.html.twig', [
            'facture' => $facture,
            'form'    => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_factures_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Factures $facture): Response
    {
        // Vérifier que la facture est dans un dossier visible
        /** @var \App\Entity\User $currentUser */
        $currentUser = $this->getUser();
        if ($facture->getDossier() !== null
            && !$this->visibilityService->canViewDossier($facture->getDossier(), $currentUser)) {
            throw $this->createAccessDeniedException('Accès non autorisé à cette facture.');
        }

        return $this->render('factures/show.html.twig', [
            'facture' => $facture,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_factures_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, Factures $facture, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_AVOCAT');

        // L'avocat ne peut modifier que les factures de ses dossiers
        /** @var \App\Entity\User $currentUser */
        $currentUser = $this->getUser();
        if ($facture->getDossier() !== null
            && !$this->visibilityService->canViewDossier($facture->getDossier(), $currentUser)) {
            throw $this->createAccessDeniedException('Accès non autorisé à cette facture.');
        }

        $form = $this->createForm(FacturesType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $facture->recalculerDepuisArticles();
            $em->flush();
            $this->addFlash('success', 'Facture mise à jour avec succès.');
            return $this->redirectToRoute('app_factures_show', ['id' => $facture->getId()]);
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('error', 'Veuillez corriger les erreurs du formulaire.');
        }

        return $this->render('factures/edit.html.twig', [
            'facture' => $facture,
            'form'    => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_factures_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, Factures $facture, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_AVOCAT');

        /** @var \App\Entity\User $currentUser */
        $currentUser = $this->getUser();
        if ($facture->getDossier() !== null
            && !$this->visibilityService->canViewDossier($facture->getDossier(), $currentUser)) {
            throw $this->createAccessDeniedException('Accès non autorisé.');
        }

        if ($this->isCsrfTokenValid('delete' . $facture->getId(), $request->request->get('_token'))) {
            $em->remove($facture);
            $em->flush();
            $this->addFlash('success', 'Facture supprimée avec succès.');
        }
        return $this->redirectToRoute('app_factures_index');
    }

    #[Route('/{id}/pdf', name: 'app_factures_pdf', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function pdf(Factures $facture): Response
    {
        /** @var \App\Entity\User $currentUser */
        $currentUser = $this->getUser();
        if ($facture->getDossier() !== null
            && !$this->visibilityService->canViewDossier($facture->getDossier(), $currentUser)) {
            throw $this->createAccessDeniedException('Accès non autorisé.');
        }

        if (!class_exists(\Dompdf\Dompdf::class)) {
            $this->addFlash('error', 'Pour générer des PDFs, installez: composer require dompdf/dompdf');
            return $this->redirectToRoute('app_factures_show', ['id' => $facture->getId()]);
        }

        $options = new \Dompdf\Options();
        $options->set('defaultFont', 'Helvetica');
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new \Dompdf\Dompdf($options);
        $html   = $this->renderView('factures/pdf.html.twig', ['facture' => $facture]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $response = new Response($dompdf->output());
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'inline; filename="facture-' . $facture->getNumeroFacture() . '.pdf"');

        return $response;
    }
}
