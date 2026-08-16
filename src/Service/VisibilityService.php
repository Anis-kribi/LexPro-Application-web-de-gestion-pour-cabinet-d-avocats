<?php

namespace App\Service;

use App\Entity\User;

/**
 * Service centralisé de résolution de la visibilité par rôle.
 *
 * Logique :
 *   ROLE_ADMIN     → retourne null  (aucun filtre, tout visible)
 *   ROLE_AVOCAT    → retourne l'avocat lui-même
 *   ROLE_ASSISTANT → retourne le manager (avocat) de cet assistant
 */
class VisibilityService
{
    /**
     * Retourne l'avocat « effectif » à utiliser pour filtrer les données.
     * Retourne null si l'utilisateur est admin (aucun filtre nécessaire).
     */
    public function getEffectiveAvocat(User $user): ?User
    {
        if ($user->isAdmin()) {
            return null; // Admin voit tout
        }

        if ($user->isAvocat()) {
            return $user; // Avocat voit ses propres données
        }

        if ($user->isAssistant()) {
            return $user->getManager(); // Assistant voit les données de son avocat
        }

        return $user; // Fallback sécurisé
    }

    /**
     * Vérifie si l'utilisateur peut voir un dossier donné.
     */
    public function canViewDossier(\App\Entity\Dossier $dossier, User $user): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        $effectiveAvocat = $this->getEffectiveAvocat($user);

        if ($effectiveAvocat === null) {
            return true;
        }

        return $dossier->getAvocat() !== null
            && $dossier->getAvocat()->getId() === $effectiveAvocat->getId();
    }

    /**
     * Vérifie si l'utilisateur peut voir un client donné.
     * Le client est visible s'il a le même avocat effectif que l'utilisateur,
     * OU s'il a un dossier assigné à cet avocat.
     */
    public function canViewClient(\App\Entity\Client $client, User $user): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        $effectiveAvocat = $this->getEffectiveAvocat($user);

        if ($effectiveAvocat === null) {
            return true;
        }

        // Vérifie si le client est directement assigné à cet avocat
        if ($client->getAvocat() !== null && $client->getAvocat()->getId() === $effectiveAvocat->getId()) {
            return true;
        }

        // Sinon, on vérifie via ses dossiers (legacy support / cascade logic)
        foreach ($client->getDossiers() as $dossier) {
            if ($dossier->getAvocat() !== null && $dossier->getAvocat()->getId() === $effectiveAvocat->getId()) {
                return true;
            }
        }

        return false;
    }
}
