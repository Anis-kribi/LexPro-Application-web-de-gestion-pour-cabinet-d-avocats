<?php

namespace App\Entity;

use App\Repository\DocumentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DocumentRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Document
{
    // Types de document
    const TYPE_CONTRAT      = 'Contrat';
    const TYPE_JUGEMENT     = 'Jugement';
    const TYPE_PLAINTE      = 'Plainte';
    const TYPE_PROCURATION  = 'Procuration';
    const TYPE_JUSTIFICATIF = 'Justificatif';
    const TYPE_CORRESPONDANCE = 'Correspondance';
    const TYPE_AUTRE        = 'Autre';

    // Niveaux de confidentialité
    const CONF_PUBLIC       = 'Public';
    const CONF_INTERNE      = 'Interne';
    const CONF_CONFIDENTIEL = 'Confidentiel';
    const CONF_SECRET       = 'Secret';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /** Relation vers le dossier */
    #[ORM\ManyToOne(inversedBy: 'documents')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Le dossier est obligatoire.')]
    private ?Dossier $dossier = null;

    /** Utilisateur qui a uploadé le document */
    #[ORM\ManyToOne(inversedBy: 'documents')]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $telechargepar = null;

    /** Titre affiché du document */
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le titre est obligatoire.')]
    private ?string $titre = null;

    /** Chemin physique du fichier sur le serveur */
    #[ORM\Column(length: 500)]
    private ?string $cheminFichier = null;

    /** Nom original du fichier uploadé */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nomOriginal = null;

    /** Type de document */
    #[ORM\Column(length: 100)]
    private ?string $type = self::TYPE_AUTRE;

    /** Niveau de confidentialité */
    #[ORM\Column(length: 50)]
    private ?string $confidentialite = self::CONF_INTERNE;

    /** Date de création */
    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        if ($this->createdAt === null) {
            $this->createdAt = new \DateTime();
        }
    }

    // --- Getters & Setters ---

    public function getId(): ?int { return $this->id; }

    public function getDossier(): ?Dossier { return $this->dossier; }
    public function setDossier(?Dossier $dossier): static { $this->dossier = $dossier; return $this; }

    public function getTelechargepar(): ?User { return $this->telechargepar; }
    public function setTelechargepar(?User $telechargepar): static { $this->telechargepar = $telechargepar; return $this; }

    public function getTitre(): ?string { return $this->titre; }
    public function setTitre(string $titre): static { $this->titre = $titre; return $this; }

    public function getCheminFichier(): ?string { return $this->cheminFichier; }
    public function setCheminFichier(string $cheminFichier): static { $this->cheminFichier = $cheminFichier; return $this; }

    public function getNomOriginal(): ?string { return $this->nomOriginal; }
    public function setNomOriginal(?string $nomOriginal): static { $this->nomOriginal = $nomOriginal; return $this; }

    public function getType(): ?string { return $this->type; }
    public function setType(string $type): static { $this->type = $type; return $this; }

    public function getConfidentialite(): ?string { return $this->confidentialite; }
    public function setConfidentialite(string $confidentialite): static { $this->confidentialite = $confidentialite; return $this; }

    public function getCreatedAt(): ?\DateTimeInterface { return $this->createdAt; }
    public function setCreatedAt(\DateTimeInterface $createdAt): static { $this->createdAt = $createdAt; return $this; }

    /** Extension du fichier pour affichage icône */
    public function getExtension(): string
    {
        return strtolower(pathinfo($this->cheminFichier ?? '', PATHINFO_EXTENSION));
    }
}
