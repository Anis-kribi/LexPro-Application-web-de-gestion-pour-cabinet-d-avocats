<?php

namespace App\Controller;

use App\Form\ProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

#[IsGranted('ROLE_ASSISTANT')]
class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher,
        SluggerInterface $slugger
    ): Response {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            // Handle image upload independently (before full validation)
            // so it works even if only photo is changed
            $imageFile = $form->get('imageFile')->getData();

            if ($form->isValid()) {
                $currentPasswordRaw = $form->get('currentPassword')->getData();
                $newPasswordRaw     = $form->get('newPassword')->getData();

                // Si l'utilisateur veut changer de mot de passe
                if ($newPasswordRaw) {
                    if (!$currentPasswordRaw) {
                        $this->addFlash('error', 'Veuillez saisir votre mot de passe actuel pour le modifier.');
                        return $this->redirectToRoute('app_profile');
                    }

                    if (!$hasher->isPasswordValid($user, $currentPasswordRaw)) {
                        $this->addFlash('error', 'Le mot de passe actuel est incorrect.');
                        return $this->redirectToRoute('app_profile');
                    }

                    $user->setPassword($hasher->hashPassword($user, $newPasswordRaw));
                }

                // Gestion de l'upload d'image
                if ($imageFile) {
                    $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                    try {
                        $imageFile->move(
                            $this->getParameter('kernel.project_dir') . '/public/uploads/users',
                            $newFilename
                        );

                        // Delete old image if exists
                        if ($user->getImage()) {
                            $oldImagePath = $this->getParameter('kernel.project_dir') . '/public/uploads/users/' . $user->getImage();
                            if (file_exists($oldImagePath)) {
                                unlink($oldImagePath);
                            }
                        }

                        $user->setImage($newFilename);
                    } catch (FileException $e) {
                        $this->addFlash('error', 'Erreur lors du téléchargement de l\'image: ' . $e->getMessage());
                    }
                }

                $em->flush();
                $this->addFlash('success', 'Profil mis à jour avec succès.');
                return $this->redirectToRoute('app_profile');
            } else {
                // Collect form errors and show them
                foreach ($form->getErrors(true) as $error) {
                    $this->addFlash('error', $error->getMessage());
                }
            }
        }

        return $this->render('profile/index.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }
}
