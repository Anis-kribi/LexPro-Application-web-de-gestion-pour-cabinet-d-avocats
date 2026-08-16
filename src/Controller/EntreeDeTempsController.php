<?php

namespace App\Controller;

use App\Entity\EntreeDeTemps;
use App\Form\EntreeDeTempsType;
use App\Repository\EntreeDeTempsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/entrees-temps')]
final class EntreeDeTempsController extends AbstractController
{
    #[Route('/', name: 'app_entree_temps_index', methods: ['GET'])]
    public function index(Request $request, EntreeDeTempsRepository $repo): Response
    {
        /** @var \App\Entity\User $user */
        $user   = $this->getUser();
        $entrees = $repo->findByUser($user);

        $totalHeuresMois = $repo->getTotalHeuresFacturablesMoisCourant();

        return $this->render('entree_temps/index.html.twig', [
            'entrees'         => $entrees,
            'totalHeuresMois' => $totalHeuresMois,
        ]);
    }

    #[Route('/new', name: 'app_entree_temps_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $entree = new EntreeDeTemps();

        // Pré-remplir l'utilisateur connecté
        $entree->setUser($this->getUser());
        $entree->setDate(new \DateTime());

        $form = $this->createForm(EntreeDeTempsType::class, $entree);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($entree);
            $em->flush();
            $this->addFlash('success', 'Entrée de temps enregistrée.');
            return $this->redirectToRoute('app_entree_temps_index');
        }

        return $this->render('entree_temps/new.html.twig', [
            'entree' => $entree,
            'form'   => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_entree_temps_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, EntreeDeTemps $entree, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(EntreeDeTempsType::class, $entree);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Entrée de temps modifiée.');
            return $this->redirectToRoute('app_entree_temps_index');
        }

        return $this->render('entree_temps/edit.html.twig', [
            'entree' => $entree,
            'form'   => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_entree_temps_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, EntreeDeTemps $entree, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $entree->getId(), $request->request->get('_token'))) {
            $em->remove($entree);
            $em->flush();
            $this->addFlash('success', 'Entrée de temps supprimée.');
        }
        return $this->redirectToRoute('app_entree_temps_index');
    }
}
