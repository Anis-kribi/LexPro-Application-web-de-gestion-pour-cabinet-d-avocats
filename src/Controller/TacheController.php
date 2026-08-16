<?php

namespace App\Controller;

use App\Entity\Tache;
use App\Form\TacheType;
use App\Repository\TacheRepository;
use App\Service\VisibilityService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/taches')]
final class TacheController extends AbstractController
{
    public function __construct(private readonly VisibilityService $visibilityService) {}

    #[Route('/', name: 'app_tache_index', methods: ['GET'])]
    public function index(Request $request, TacheRepository $tacheRepository): Response
    {
        /** @var \App\Entity\User $currentUser */
        $currentUser     = $this->getUser();
        $effectiveAvocat = $this->visibilityService->getEffectiveAvocat($currentUser);

        $status   = $request->query->get('status');
        $priorite = $request->query->get('priorite');

        if ($status) {
            $taches = $tacheRepository->findByStatutForAvocat($effectiveAvocat, $status);
        } else {
            $taches = $tacheRepository->findVisibleByAvocat($effectiveAvocat);
        }

        $statsByStatut = $tacheRepository->countByStatutForAvocat($effectiveAvocat);
        $urgentes      = $tacheRepository->findUrgentesForAvocat($effectiveAvocat);

        return $this->render('tache/index.html.twig', [
            'taches'        => $taches,
            'statsByStatut' => $statsByStatut,
            'urgentes'      => $urgentes,
            'status'        => $status,
            'priorite'      => $priorite,
        ]);
    }

    #[Route('/new', name: 'app_tache_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $tache = new Tache();
        $form  = $this->createForm(TacheType::class, $tache);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($tache);
            $em->flush();
            $this->addFlash('success', 'Tâche créée avec succès.');
            return $this->redirectToRoute('app_tache_index');
        }

        return $this->render('tache/new.html.twig', [
            'tache' => $tache,
            'form'  => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_tache_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Tache $tache): Response
    {
        // Vérifier que la tâche appartient à un dossier visible
        /** @var \App\Entity\User $currentUser */
        $currentUser = $this->getUser();
        if ($tache->getDossier() !== null && !$this->visibilityService->canViewDossier($tache->getDossier(), $currentUser)) {
            throw $this->createAccessDeniedException('Accès non autorisé à cette tâche.');
        }

        return $this->render('tache/show.html.twig', ['tache' => $tache]);
    }

    #[Route('/{id}/edit', name: 'app_tache_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, Tache $tache, EntityManagerInterface $em): Response
    {
        // Vérifier que la tâche appartient à un dossier visible
        /** @var \App\Entity\User $currentUser */
        $currentUser = $this->getUser();
        if ($tache->getDossier() !== null && !$this->visibilityService->canViewDossier($tache->getDossier(), $currentUser)) {
            throw $this->createAccessDeniedException('Accès non autorisé à cette tâche.');
        }

        $form = $this->createForm(TacheType::class, $tache);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Tâche mise à jour.');
            return $this->redirectToRoute('app_tache_index');
        }

        return $this->render('tache/edit.html.twig', [
            'tache' => $tache,
            'form'  => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_tache_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, Tache $tache, EntityManagerInterface $em): Response
    {
        /** @var \App\Entity\User $currentUser */
        $currentUser = $this->getUser();
        if ($tache->getDossier() !== null && !$this->visibilityService->canViewDossier($tache->getDossier(), $currentUser)) {
            throw $this->createAccessDeniedException('Accès non autorisé.');
        }

        if ($this->isCsrfTokenValid('delete' . $tache->getId(), $request->request->get('_token'))) {
            $em->remove($tache);
            $em->flush();
            $this->addFlash('success', 'Tâche supprimée.');
        }
        return $this->redirectToRoute('app_tache_index');
    }

    /** Changement rapide de statut via AJAX ou simple POST */
    #[Route('/{id}/status/{status}', name: 'app_tache_change_status', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function changeStatus(Tache $tache, string $status, EntityManagerInterface $em): Response
    {
        /** @var \App\Entity\User $currentUser */
        $currentUser = $this->getUser();
        if ($tache->getDossier() !== null && !$this->visibilityService->canViewDossier($tache->getDossier(), $currentUser)) {
            throw $this->createAccessDeniedException('Accès non autorisé.');
        }

        $allowed = ['À faire', 'En cours', 'Terminée', 'Annulée'];
        if (in_array($status, $allowed)) {
            $tache->setStatus($status);
            $em->flush();
            $this->addFlash('success', 'Statut mis à jour : ' . $status);
        }
        return $this->redirectToRoute('app_tache_index');
    }
}
