<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{
    public const USER_CREATE = 'USER_CREATE';
    public const USER_EDIT   = 'USER_EDIT';
    public const USER_DELETE = 'USER_DELETE';

    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::USER_CREATE, self::USER_EDIT, self::USER_DELETE], true);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $currentUser = $token->getUser();

        if (!$currentUser instanceof User) {
            return false;
        }

        // Seul l'ADMIN peut gérer les utilisateurs
        if (!in_array('ROLE_ADMIN', $currentUser->getRoles(), true)) {
            return false;
        }

        if ($attribute === self::USER_EDIT && $subject instanceof User) {
            if (in_array('ROLE_ADMIN', $subject->getRoles(), true)) {
                return false; // L'admin se modifie via /profile uniquement
            }
        }

        // Protection supplémentaire : empêcher la suppression du dernier admin
        if ($attribute === self::USER_DELETE && $subject instanceof User) {
            if (in_array('ROLE_ADMIN', $subject->getRoles(), true)) {
                // Vérifie qu'il existe un autre admin
                return $this->userRepository->hasOtherAdmin($subject->getId());
            }
        }

        return true;
    }
}
