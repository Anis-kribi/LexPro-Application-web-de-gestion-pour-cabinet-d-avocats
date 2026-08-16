<?php

namespace App\Controller;

use App\Entity\RendezVous;
use App\Form\RendezVousType;
use App\Repository\RendezVousRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/rendez/vous')]
final class RendezVousController extends AbstractController
{
    #[Route('/', name: 'app_rendez_vous_index', methods: ['GET'])]
    public function index(RendezVousRepository $rendezVousRepository): Response
    {
        return $this->render('rendez_vous/index.html.twig', [
            'rendezvous' => $rendezVousRepository->findAll(),
        ]);
    }

    #[Route('/calendar', name: 'app_rendez_vous_calendar', methods: ['GET'])]
    public function calendar(): Response
    {
        return $this->render('rendez_vous/calendar.html.twig');
    }

    #[Route('/events', name: 'app_rendez_vous_events', methods: ['GET'])]
    public function events(RendezVousRepository $rendezVousRepository): Response
    {
        $rendezvous = $rendezVousRepository->findAll();
        $rdvs = [];

        foreach($rendezvous as $rdv){
            $rdvs[] = [
                'id' => $rdv->getId(),
                'start' => $rdv->getDate()->format('Y-m-d H:i:s'),
                'title' => 'RDV: ' . $rdv->getClient()->getFullName(),
                'backgroundColor' => $rdv->getStatus() === 'Confirmé' ? '#10b981' : ($rdv->getStatus() === 'Annulé' ? '#ef4444' : '#f59e0b'),
                'borderColor' => $rdv->getStatus() === 'Confirmé' ? '#10b981' : ($rdv->getStatus() === 'Annulé' ? '#ef4444' : '#f59e0b'),
                'url' => $this->generateUrl('app_rendez_vous_show', ['id' => $rdv->getId()]),
            ];
        }

        $data = json_encode($rdvs);

        return new Response($data, 200, [
            'Content-Type' => 'application/json'
        ]);
    }

    #[Route('/new', name: 'app_rendez_vous_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $rendezVou = new RendezVous();
        $form = $this->createForm(RendezVousType::class, $rendezVou);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($rendezVou);
            $entityManager->flush();

            $this->addFlash('success', 'Rendez-vous créé avec succès!');
            return $this->redirectToRoute('app_rendez_vous_index');
        }

        return $this->render('rendez_vous/new.html.twig', [
            'rendez_vou' => $rendezVou,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_rendez_vous_show', methods: ['GET'], requirements: ['id' => '[1-9]\d*'])]
    public function show(RendezVous $rendezVou): Response
    {
        return $this->render('rendez_vous/show.html.twig', [
            'rendez_vou' => $rendezVou,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_rendez_vous_edit', methods: ['GET', 'POST'], requirements: ['id' => '[1-9]\d*'])]
    public function edit(Request $request, RendezVous $rendezVou, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RendezVousType::class, $rendezVou);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Rendez-vous mis à jour avec succès!');
            return $this->redirectToRoute('app_rendez_vous_index');
        }

        return $this->render('rendez_vous/edit.html.twig', [
            'rendez_vou' => $rendezVou,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_rendez_vous_delete', methods: ['POST'], requirements: ['id' => '[1-9]\d*'])]
    public function delete(Request $request, RendezVous $rendezVou, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$rendezVou->getId(), $request->request->get('_token'))) {
            $entityManager->remove($rendezVou);
            $entityManager->flush();

            $this->addFlash('success', 'Rendez-vous supprimé avec succès!');
        }

        return $this->redirectToRoute('app_rendez_vous_index');
    }
}
