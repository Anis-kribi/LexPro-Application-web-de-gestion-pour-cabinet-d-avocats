<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Security\Voter\UserVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Service\FileUploader;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[Route('/users')]
#[IsGranted('ROLE_ADMIN')]
final class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAllOrderedByDate(),
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher,
        UserRepository $userRepository,
        FileUploader $fileUploader,
        #[Autowire('%users_directory%')] string $usersDirectory
    ): Response {
        $this->denyAccessUnlessGranted(UserVoter::USER_CREATE);

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $selectedRole = $form->get('roles')->getData();

            // Sécurité : bloquer création d'un 2ème admin via ce formulaire
            if ($selectedRole === 'ROLE_ADMIN') {
                $this->addFlash('error', 'Il ne peut y avoir qu\'un seul administrateur.');
                return $this->redirectToRoute('app_user_new');
            }

            $user->setRoles([$selectedRole]);

            // Vérification du manager pour les assistants
            if ($selectedRole === 'ROLE_ASSISTANT' && $user->getManager() === null) {
                $this->addFlash('error', 'Un assistant doit obligatoirement être rattaché à un avocat responsable.');
                return $this->render('user/new.html.twig', [
                    'form' => $form->createView(),
                    'user' => $user,
                ]);
            }
            if ($selectedRole !== 'ROLE_ASSISTANT') {
                $user->setManager(null);
            }

            $plain = $form->get('plainPassword')->getData();
            if ($plain) {
                $user->setPassword($hasher->hashPassword($user, $plain));
            }

            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $imageFileName = $fileUploader->upload($imageFile, $usersDirectory);
                $user->setImage($imageFileName);
            }

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', sprintf(
                'Utilisateur %s créé avec succès.',
                $user->getFullName()
            ));

            return $this->redirectToRoute('app_user_index');
        }

        return $this->render('user/new.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(
        Request $request,
        User $user,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher,
        FileUploader $fileUploader,
        #[Autowire('%users_directory%')] string $usersDirectory
    ): Response {
        $this->denyAccessUnlessGranted(UserVoter::USER_EDIT, $user);

        // Déterminer le rôle actuel principal pour préremplir le champ non mappé 'roles'
        $roles = $user->getRoles();
        $currentRole = 'ROLE_ASSISTANT'; // fallback
        if (in_array('ROLE_ADMIN', $roles, true)) {
            $currentRole = 'ROLE_ADMIN';
        } elseif (in_array('ROLE_AVOCAT', $roles, true)) {
            $currentRole = 'ROLE_AVOCAT';
        }

        $form = $this->createForm(UserType::class, $user, [
            'is_edit' => true,
            'current_role' => $currentRole
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $selectedRole = $form->get('roles')->getData();

            // Empêcher de downgrader l'admin ou d'upgrader quelqu'un en admin
            if ($selectedRole !== 'ROLE_ADMIN') {
                $user->setRoles([$selectedRole]);
            }

            // Vérification du manager pour les assistants
            if ($selectedRole === 'ROLE_ASSISTANT' && $user->getManager() === null) {
                $this->addFlash('error', 'Un assistant doit obligatoirement être rattaché à un avocat responsable.');
                return $this->render('user/edit.html.twig', [
                    'form' => $form->createView(),
                    'user' => $user,
                ]);
            }
            if ($selectedRole !== 'ROLE_ASSISTANT') {
                $user->setManager(null);
            }

            $plain = $form->get('plainPassword')->getData();
            if ($plain) {
                $user->setPassword($hasher->hashPassword($user, $plain));
            }

            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $fileUploader->remove($usersDirectory, $user->getImage());
                $imageFileName = $fileUploader->upload($imageFile, $usersDirectory);
                $user->setImage($imageFileName);
            }

            $em->flush();

            $this->addFlash('success', sprintf(
                'Utilisateur %s mis à jour.',
                $user->getFullName()
            ));

            return $this->redirectToRoute('app_user_index');
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(
        Request $request, 
        User $user, 
        EntityManagerInterface $em,
        FileUploader $fileUploader,
        #[Autowire('%users_directory%')] string $usersDirectory
    ): Response {
        $this->denyAccessUnlessGranted(UserVoter::USER_DELETE, $user);

        if ($this->isCsrfTokenValid('delete_user_' . $user->getId(), $request->request->get('_token'))) {
            if ($user->getImage()) {
                $fileUploader->remove($usersDirectory, $user->getImage());
            }
            $em->remove($user);
            $em->flush();
            $this->addFlash('success', 'Utilisateur supprimé.');
        } else {
            $this->addFlash('error', 'Token CSRF invalide.');
        }

        return $this->redirectToRoute('app_user_index');
    }
}
