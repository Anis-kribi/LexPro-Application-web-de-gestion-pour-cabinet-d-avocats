<?php

namespace App\Controller;

use App\Entity\Dossier;
use App\Form\DossierType;
use App\Repository\DossierRepository;
use App\Security\Voter\DossierVoter;
use App\Service\VisibilityService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dossiers')]
final class DossierController extends AbstractController
{
    public function __construct(private readonly VisibilityService $visibilityService) {}

    #[Route('/', name: 'app_dossier_index', methods: ['GET'])]
    public function index(Request $request, DossierRepository $dossierRepository): Response
    {
        /** @var \App\Entity\User $currentUser */
        $currentUser     = $this->getUser();
        $effectiveAvocat = $this->visibilityService->getEffectiveAvocat($currentUser);

        $keyword  = $request->query->get('search');
        $statut   = $request->query->get('statut');
        $priorite = $request->query->get('priorite');
        $clientId = $request->query->get('client');
        $page     = max(1, (int) $request->query->get('page', 1));
        $limit    = 15;

        $result = $dossierRepository->findWithFiltersForAvocat(
            $effectiveAvocat,
            $keyword,
            $statut ?: null,
            $priorite ?: null,
            $clientId ? (int) $clientId : null,
            $page,
            $limit
        );

        $statsByStatut = $dossierRepository->countByStatutForAvocat($effectiveAvocat);

        return $this->render('dossier/index.html.twig', [
            'dossiers'      => $result['data'],
            'total'         => $result['total'],
            'page'          => $page,
            'totalPages'    => (int) ceil($result['total'] / $limit),
            'keyword'       => $keyword,
            'statut'        => $statut,
            'priorite'      => $priorite,
            'statsByStatut' => $statsByStatut,
        ]);
    }

    #[Route('/new', name: 'app_dossier_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted(DossierVoter::DOSSIER_CREATE);
        $dossier = new Dossier();
        $form    = $this->createForm(DossierType::class, $dossier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$dossier->getNumeroReference()) {
                $dossier->setNumeroReference('DOS-' . date('Y') . '-' . str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT));
            }

            // Si l'utilisateur n'est pas un admin, on assigne automatiquement son avocat effectif
            /** @var \App\Entity\User $currentUser */
            $currentUser = $this->getUser();
            if (!$this->isGranted('ROLE_ADMIN')) {
                $effectiveAvocat = $this->visibilityService->getEffectiveAvocat($currentUser);
                $dossier->setAvocat($effectiveAvocat);
            }

            $em->persist($dossier);
            $em->flush();
            $this->addFlash('success', 'Dossier créé avec succès.');
            return $this->redirectToRoute('app_dossier_show', ['id' => $dossier->getId()]);
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('error', 'Veuillez corriger les erreurs du formulaire.');
        }

        return $this->render('dossier/new.html.twig', [
            'dossier' => $dossier,
            'form'    => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_dossier_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Dossier $dossier): Response
    {
        // Vérification ownership : l'utilisateur doit pouvoir voir ce dossier
        $this->denyAccessUnlessGranted(DossierVoter::DOSSIER_VIEW, $dossier);

        return $this->render('dossier/show.html.twig', [
            'dossier' => $dossier,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_dossier_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, Dossier $dossier, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted(DossierVoter::DOSSIER_EDIT, $dossier);
        $form = $this->createForm(DossierType::class, $dossier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Dossier mis à jour avec succès.');
            return $this->redirectToRoute('app_dossier_show', ['id' => $dossier->getId()]);
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('error', 'Veuillez corriger les erreurs du formulaire.');
        }

        return $this->render('dossier/edit.html.twig', [
            'dossier' => $dossier,
            'form'    => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_dossier_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, Dossier $dossier, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted(DossierVoter::DOSSIER_DELETE, $dossier);
        if ($this->isCsrfTokenValid('delete' . $dossier->getId(), $request->request->get('_token'))) {
            $em->remove($dossier);
            $em->flush();
            $this->addFlash('success', 'Dossier supprimé avec succès.');
        }
        return $this->redirectToRoute('app_dossier_index');
    }
}
