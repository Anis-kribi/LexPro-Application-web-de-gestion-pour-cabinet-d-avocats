<?php

namespace App\Controller;

use App\Entity\Document;
use App\Form\DocumentType;
use App\Repository\DocumentRepository;
use App\Security\Voter\DocumentVoter;
use App\Service\VisibilityService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/documents')]
final class DocumentController extends AbstractController
{
    public function __construct(private readonly VisibilityService $visibilityService) {}

    #[Route('/', name: 'app_document_index', methods: ['GET'])]
    public function index(Request $request, DocumentRepository $documentRepository): Response
    {
        /** @var \App\Entity\User $currentUser */
        $currentUser     = $this->getUser();
        $effectiveAvocat = $this->visibilityService->getEffectiveAvocat($currentUser);

        $keyword = $request->query->get('search');
        $type    = $request->query->get('type');

        if ($keyword) {
            $documents = $documentRepository->findByKeywordForAvocat($effectiveAvocat, $keyword);
        } elseif ($type) {
            $documents = $documentRepository->findByTypeForAvocat($effectiveAvocat, $type);
        } else {
            $documents = $documentRepository->findVisibleByAvocat($effectiveAvocat);
        }

        return $this->render('document/index.html.twig', [
            'documents' => $documents,
            'keyword'   => $keyword,
            'type'      => $type,
        ]);
    }

    #[Route('/new', name: 'app_document_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted(DocumentVoter::DOCUMENT_CREATE);
        $document = new Document();
        $form     = $this->createForm(DocumentType::class, $document);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $form->get('file')->getData();

            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename     = $slugger->slug($originalFilename);
                $newFilename      = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

                try {
                    $file->move($this->getParameter('documents_directory'), $newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', "Erreur lors de l'upload : " . $e->getMessage());
                    return $this->redirectToRoute('app_document_new');
                }

                $document->setCheminFichier($newFilename);
                $document->setNomOriginal($file->getClientOriginalName());
            }

            $document->setTelechargepar($this->getUser());

            $em->persist($document);
            $em->flush();

            $this->addFlash('success', 'Document ajouté avec succès.');
            return $this->redirectToRoute('app_document_index');
        }

        return $this->render('document/new.html.twig', [
            'document' => $document,
            'form'     => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_document_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Document $document): Response
    {
        $this->denyAccessUnlessGranted(DocumentVoter::DOCUMENT_VIEW, $document);

        return $this->render('document/show.html.twig', [
            'document' => $document,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_document_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, Document $document, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted(DocumentVoter::DOCUMENT_EDIT, $document);
        $form = $this->createForm(DocumentType::class, $document, ['is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile|null $file */
            $file = $form->get('file')->getData();

            if ($file) {
                $ancienFichier = $this->getParameter('documents_directory') . '/' . $document->getCheminFichier();
                if (file_exists($ancienFichier)) {
                    unlink($ancienFichier);
                }

                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename     = $slugger->slug($originalFilename);
                $newFilename      = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

                try {
                    $file->move($this->getParameter('documents_directory'), $newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', "Erreur lors de l'upload : " . $e->getMessage());
                    return $this->redirectToRoute('app_document_edit', ['id' => $document->getId()]);
                }

                $document->setCheminFichier($newFilename);
                $document->setNomOriginal($file->getClientOriginalName());
            }

            $em->flush();
            $this->addFlash('success', 'Document modifié avec succès.');
            return $this->redirectToRoute('app_document_show', ['id' => $document->getId()]);
        }

        return $this->render('document/edit.html.twig', [
            'document' => $document,
            'form'     => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_document_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, Document $document, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted(DocumentVoter::DOCUMENT_DELETE, $document);
        if ($this->isCsrfTokenValid('delete' . $document->getId(), $request->request->get('_token'))) {
            $fichier = $this->getParameter('documents_directory') . '/' . $document->getCheminFichier();
            if (file_exists($fichier)) {
                unlink($fichier);
            }
            $em->remove($document);
            $em->flush();
            $this->addFlash('success', 'Document supprimé avec succès.');
        }
        return $this->redirectToRoute('app_document_index');
    }

    #[Route('/{id}/download', name: 'app_document_download', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function download(Document $document): Response
    {
        $this->denyAccessUnlessGranted(DocumentVoter::DOCUMENT_VIEW, $document);

        $file = $this->getParameter('documents_directory') . '/' . $document->getCheminFichier();

        if (!file_exists($file)) {
            $this->addFlash('error', 'Le fichier physique est introuvable sur le serveur.');
            return $this->redirectToRoute('app_document_index');
        }

        return $this->file($file, $document->getNomOriginal() ?? $document->getTitre());
    }
}
