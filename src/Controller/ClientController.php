<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientType;
use App\Repository\ClientRepository;
use App\Security\Voter\ClientVoter;
use App\Service\VisibilityService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\FileUploader;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[Route('/client')]
final class ClientController extends AbstractController
{
    public function __construct(private readonly VisibilityService $visibilityService) {}

    #[Route('/', name: 'app_client_index', methods: ['GET'])]
    public function index(Request $request, ClientRepository $clientRepository): Response
    {
        /** @var \App\Entity\User $currentUser */
        $currentUser     = $this->getUser();
        $effectiveAvocat = $this->visibilityService->getEffectiveAvocat($currentUser);

        $keyword = $request->query->get('search');
        $statut  = $request->query->get('statut');
        $type    = $request->query->get('type');
        $page    = max(1, (int) $request->query->get('page', 1));
        $limit   = 15;

        $result = $clientRepository->findWithFiltersForAvocat(
            $effectiveAvocat,
            $keyword,
            $statut ?: null,
            $type ?: null,
            $page,
            $limit
        );

        $totalPages = (int) ceil($result['total'] / $limit);

        return $this->render('client/index.html.twig', [
            'clients'     => $result['data'],
            'total'       => $result['total'],
            'page'        => $page,
            'totalPages'  => $totalPages,
            'limit'       => $limit,
            'keyword'     => $keyword,
            'statut'      => $statut,
            'type'        => $type,
        ]);
    }

    #[Route('/new', name: 'app_client_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        FileUploader $fileUploader,
        #[Autowire('%clients_directory%')] string $clientsDirectory
    ): Response
    {
        $this->denyAccessUnlessGranted(ClientVoter::CLIENT_CREATE);
        $client = new Client();
        $form   = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $imageFileName = $fileUploader->upload($imageFile, $clientsDirectory);
                $client->setImage($imageFileName);
            }

            // Si l'utilisateur n'est pas un admin, on assigne automatiquement son avocat effectif
            /** @var \App\Entity\User $currentUser */
            $currentUser = $this->getUser();
            if (!$this->isGranted('ROLE_ADMIN')) {
                $effectiveAvocat = $this->visibilityService->getEffectiveAvocat($currentUser);
                $client->setAvocat($effectiveAvocat);
            }

            $em->persist($client);
            $em->flush();
            $this->addFlash('success', 'Client ajouté avec succès.');
            return $this->redirectToRoute('app_client_index');
        }

        return $this->render('client/new.html.twig', [
            'form'   => $form->createView(),
            'client' => $client,
        ]);
    }

    #[Route('/{id}', name: 'app_client_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Client $client): Response
    {
        $this->denyAccessUnlessGranted(ClientVoter::CLIENT_VIEW, $client);

        return $this->render('client/show.html.twig', [
            'client' => $client,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_client_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(
        Request $request,
        Client $client,
        EntityManagerInterface $em,
        FileUploader $fileUploader,
        #[Autowire('%clients_directory%')] string $clientsDirectory
    ): Response
    {
        $this->denyAccessUnlessGranted(ClientVoter::CLIENT_EDIT, $client);
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $fileUploader->remove($clientsDirectory, $client->getImage());
                $imageFileName = $fileUploader->upload($imageFile, $clientsDirectory);
                $client->setImage($imageFileName);
            }
            $em->flush();
            $this->addFlash('success', 'Client mis à jour avec succès.');
            return $this->redirectToRoute('app_client_show', ['id' => $client->getId()]);
        }

        return $this->render('client/edit.html.twig', [
            'form'   => $form->createView(),
            'client' => $client,
        ]);
    }

    #[Route('/{id}', name: 'app_client_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(
        Request $request,
        Client $client,
        EntityManagerInterface $em,
        FileUploader $fileUploader,
        #[Autowire('%clients_directory%')] string $clientsDirectory
    ): Response
    {
        $this->denyAccessUnlessGranted(ClientVoter::CLIENT_DELETE, $client);
        if ($this->isCsrfTokenValid('delete' . $client->getId(), $request->request->get('_token'))) {
            if ($client->getImage()) {
                $fileUploader->remove($clientsDirectory, $client->getImage());
            }
            $em->remove($client);
            $em->flush();
            $this->addFlash('success', 'Client supprimé avec succès.');
        }
        return $this->redirectToRoute('app_client_index');
    }
}
