<?php

namespace App\Security\Voter;

use App\Entity\Client;
use App\Entity\User;
use App\Service\VisibilityService;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ClientVoter extends Voter
{
    public const CLIENT_VIEW   = 'CLIENT_VIEW';
    public const CLIENT_CREATE = 'CLIENT_CREATE';
    public const CLIENT_EDIT   = 'CLIENT_EDIT';
    public const CLIENT_DELETE = 'CLIENT_DELETE';

    public function __construct(private readonly VisibilityService $visibilityService) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [
            self::CLIENT_VIEW,
            self::CLIENT_CREATE,
            self::CLIENT_EDIT,
            self::CLIENT_DELETE,
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
            // Lecture : vérifiée par le VisibilityService
            self::CLIENT_VIEW => $this->visibilityService->canViewClient($subject, $user),

            // Création : Admin, Avocat, et Assistant
            self::CLIENT_CREATE => $isAdmin || $isAvocat || $isAssistant,

            // Modification/Suppression : Admin seulement, ou Avocat/Assistant si le client
            // appartient à leurs dossiers/ceux de leur manager
            self::CLIENT_EDIT,
            self::CLIENT_DELETE => $this->canManage($subject, $user, $isAdmin, $isAvocat, $isAssistant),

            default => false,
        };
    }

    private function canManage(mixed $client, User $user, bool $isAdmin, bool $isAvocat, bool $isAssistant): bool
    {
        if ($isAdmin) return true;

        if (!$isAvocat && !$isAssistant) return false;

        if (!$client instanceof Client) return true;

        // Déterminer l'avocat effectif à vérifier (l'utilisateur lui-même ou son manager s'il est assistant)
        $manager = $isAssistant ? $user->getManager() : null;
        $checkId = ($isAssistant && $manager) ? $manager->getId() : $user->getId();

        // Un avocat (ou son assistant) peut gérer le client s'il est l'avocat principal du client
        if ($client->getAvocat() !== null && $client->getAvocat()->getId() === $checkId) {
            return true;
        }

        // L'avocat (ou son assistant) peut gérer un client si au moins un de ses dossiers lui appartient
        foreach ($client->getDossiers() as $dossier) {
            if ($dossier->getAvocat() !== null && $dossier->getAvocat()->getId() === $checkId) {
                return true;
            }
        }

        return false;
    }
}
