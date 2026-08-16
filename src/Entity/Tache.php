<?php

namespace App\Entity;

use App\Repository\TacheRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TacheRepository::class)]
class Tache
{
    // Statuts possibles
    const STATUS_A_FAIRE    = 'À faire';
    const STATUS_EN_COURS   = 'En cours';
    const STATUS_TERMINEE   = 'Terminée';
    const STATUS_ANNULEE    = 'Annulée';

    // Priorités
    const PRIORITE_BASSE    = 'Basse';
    const PRIORITE_NORMALE  = 'Normale';
    const PRIORITE_HAUTE    = 'Haute';
    const PRIORITE_URGENTE  = 'Urgente';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /** Dossier auquel appartient la tâche */
    #[ORM\ManyToOne(inversedBy: 'taches')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Le dossier est obligatoire.')]
    private ?Dossier $dossier = null;

    /** Utilisateur assigné à la tâche */
    #[ORM\ManyToOne(inversedBy: 'taches')]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $assigneA = null;

    /** Titre de la tâche */
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le titre est obligatoire.')]
    #[Assert\Length(min: 3, max: 255, minMessage: 'Le titre doit faire au moins {{ limit }} caractères')]
    private ?string $titre = null;

    /** Description détaillée */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    /** Date d'échéance */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $dateEcheance = null;

    /** Statut de la tâche */
    #[ORM\Column(length: 50)]
    private ?string $status = self::STATUS_A_FAIRE;

    /** Priorité de la tâche */
    #[ORM\Column(length: 50)]
    private ?string $priorite = self::PRIORITE_NORMALE;

    // --- Getters & Setters ---

    public function getId(): ?int { return $this->id; }

    public function getDossier(): ?Dossier { return $this->dossier; }
    public function setDossier(?Dossier $dossier): static { $this->dossier = $dossier; return $this; }

    public function getAssigneA(): ?User { return $this->assigneA; }
    public function setAssigneA(?User $assigneA): static { $this->assigneA = $assigneA; return $this; }

    public function getTitre(): ?string { return $this->titre; }
    public function setTitre(string $titre): static { $this->titre = $titre; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): static { $this->description = $description; return $this; }

    public function getDateEcheance(): ?\DateTimeInterface { return $this->dateEcheance; }
    public function setDateEcheance(?\DateTimeInterface $dateEcheance): static { $this->dateEcheance = $dateEcheance; return $this; }

    public function getStatus(): ?string { return $this->status; }
    public function setStatus(string $status): static { $this->status = $status; return $this; }

    public function getPriorite(): ?string { return $this->priorite; }
    public function setPriorite(string $priorite): static { $this->priorite = $priorite; return $this; }

    /** Vérifie si la tâche est en retard */
    public function isEnRetard(): bool
    {
        if ($this->dateEcheance === null || $this->status === self::STATUS_TERMINEE) {
            return false;
        }
        return $this->dateEcheance < new \DateTime('today');
    }
}
