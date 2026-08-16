<?php

namespace App\Entity;

use App\Repository\EntreeDeTempsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EntreeDeTempsRepository::class)]
class EntreeDeTemps
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /** Utilisateur qui a saisi cette entrée de temps */
    #[ORM\ManyToOne(inversedBy: 'entreesDeTemps')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: "L'utilisateur est obligatoire.")]
    private ?User $user = null;

    /** Dossier concerné */
    #[ORM\ManyToOne(inversedBy: 'entreesDeTemps')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Le dossier est obligatoire.')]
    private ?Dossier $dossier = null;

    /** Nombre d'heures travaillées */
    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    #[Assert\NotNull]
    #[Assert\Positive(message: 'Le nombre d\'heures doit être positif.')]
    private ?string $heures = null;

    /** Date de la prestation */
    #[ORM\Column(type: 'date')]
    #[Assert\NotNull(message: 'La date est obligatoire.')]
    private ?\DateTimeInterface $date = null;

    /** Description du travail effectué */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    /** Indique si cette entrée est facturable au client */
    #[ORM\Column]
    private ?bool $facturable = true;

    // --- Getters & Setters ---

    public function getId(): ?int { return $this->id; }

    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): static { $this->user = $user; return $this; }

    public function getDossier(): ?Dossier { return $this->dossier; }
    public function setDossier(?Dossier $dossier): static { $this->dossier = $dossier; return $this; }

    public function getHeures(): ?string { return $this->heures; }
    public function setHeures(string $heures): static { $this->heures = $heures; return $this; }

    public function getDate(): ?\DateTimeInterface { return $this->date; }
    public function setDate(\DateTimeInterface $date): static { $this->date = $date; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): static { $this->description = $description; return $this; }

    public function isFacturable(): ?bool { return $this->facturable; }
    public function setFacturable(bool $facturable): static { $this->facturable = $facturable; return $this; }
}
