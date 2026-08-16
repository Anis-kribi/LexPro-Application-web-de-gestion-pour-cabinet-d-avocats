<?php

namespace App\Security\Voter;

use App\Entity\Document;
use App\Entity\User;
use App\Service\VisibilityService;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class DocumentVoter extends Voter
{
    public const DOCUMENT_VIEW   = 'DOCUMENT_VIEW';
    public const DOCUMENT_CREATE = 'DOCUMENT_CREATE';
    public const DOCUMENT_EDIT   = 'DOCUMENT_EDIT';
    public const DOCUMENT_DELETE = 'DOCUMENT_DELETE';

    public function __construct(private readonly VisibilityService $visibilityService) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [
            self::DOCUMENT_VIEW,
            self::DOCUMENT_CREATE,
            self::DOCUMENT_EDIT,
            self::DOCUMENT_DELETE,
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
            // Lecture : tous les rôles, filtrée en amont
            self::DOCUMENT_VIEW => $isAdmin || $isAvocat || $isAssistant,

            // Création/upload : Admin, Avocat et Assistant
            self::DOCUMENT_CREATE => $isAdmin || $isAvocat || $isAssistant,

            // Modification/Suppression : Admin ou le propriétaire du document
            self::DOCUMENT_EDIT,
            self::DOCUMENT_DELETE => $this->canManage($subject, $user, $isAdmin, $isAvocat, $isAssistant),

            default => false,
        };
    }

    private function canManage(mixed $document, User $user, bool $isAdmin, bool $isAvocat, bool $isAssistant): bool
    {
        if ($isAdmin) return true;

        if (!$document instanceof Document) return $isAvocat || $isAssistant;

        // L'utilisateur peut gérer un document s'il est dans un dossier visible
        $dossier = $document->getDossier();
        if ($dossier === null) {
            return $isAvocat || $isAssistant;
        }

        return $this->visibilityService->canViewDossier($dossier, $user);
    }
}
