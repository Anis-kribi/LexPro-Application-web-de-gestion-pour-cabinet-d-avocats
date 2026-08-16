<?php

namespace App\Security\Voter;

use App\Entity\Dossier;
use App\Entity\User;
use App\Service\VisibilityService;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class DossierVoter extends Voter
{
    public const DOSSIER_CREATE = 'DOSSIER_CREATE';
    public const DOSSIER_EDIT   = 'DOSSIER_EDIT';
    public const DOSSIER_DELETE = 'DOSSIER_DELETE';
    public const DOSSIER_VIEW   = 'DOSSIER_VIEW';

    public function __construct(private readonly VisibilityService $visibilityService) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [
            self::DOSSIER_CREATE,
            self::DOSSIER_EDIT,
            self::DOSSIER_DELETE,
            self::DOSSIER_VIEW,
        ], true);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        $isAdmin     = $user->isAdmin();
        $isAvocat    = $user->isAvocat();
        $isAssistant = $user->isAssistant();

        return match ($attribute) {
            // Admin : tout ; Avocat/Assistant : lecture uniquement si le dossier est visible
            self::DOSSIER_VIEW => $this->canView($subject, $user, $isAdmin, $isAvocat, $isAssistant),

            // Création : Admin et Avocat seulement
            self::DOSSIER_CREATE => $isAdmin || $isAvocat,

            // Modification : Admin ou l'avocat propriétaire du dossier
            self::DOSSIER_EDIT => $this->canEdit($subject, $user, $isAdmin, $isAvocat),

            // Suppression : Admin ou l'avocat propriétaire
            self::DOSSIER_DELETE => $this->canEdit($subject, $user, $isAdmin, $isAvocat),

            default => false,
        };
    }

    private function canView(mixed $dossier, User $user, bool $isAdmin, bool $isAvocat, bool $isAssistant): bool
    {
        if ($isAdmin) return true;

        if (!$dossier instanceof Dossier) {
            return $isAvocat || $isAssistant; // Pour DOSSIER_CREATE sans subject
        }

        return $this->visibilityService->canViewDossier($dossier, $user);
    }

    private function canEdit(mixed $dossier, User $user, bool $isAdmin, bool $isAvocat): bool
    {
        if ($isAdmin) return true;

        if (!$isAvocat) return false;

        if (!$dossier instanceof Dossier) return true; // CREATE sans subject

        // L'avocat ne peut modifier que ses propres dossiers
        $avocat = $dossier->getAvocat();
        return $avocat !== null && $avocat->getId() === $user->getId();
    }
}
