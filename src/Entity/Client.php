<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use App\Entity\Dossier;
use App\Entity\Factures;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Client
{
    // Types de client
    const TYPE_PARTICULIER = 'particulier';
    const TYPE_ENTREPRISE = 'entreprise';

    // Statuts possibles
    const STATUT_ACTIF = 'Actif';
    const STATUT_INACTIF = 'Inactif';
    const STATUT_PROSPECT = 'Prospect';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /** Type : particulier ou entreprise */
    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Le type est obligatoire.')]
    #[Assert\Choice(choices: [self::TYPE_PARTICULIER, self::TYPE_ENTREPRISE])]
    private ?string $type = self::TYPE_PARTICULIER;

    /** Prénom (obligatoire pour particulier) */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $prenom = null;

    /** Nom de famille */
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom est obligatoire.')]
    #[Assert\Length(min: 2, max: 255, minMessage: 'Le nom doit faire au moins {{ limit }} caractères')]
    private ?string $nom = null;

    /** Nom de l'entreprise (obligatoire si type = entreprise) */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nomEntreprise = null;

    /** Numéro fiscal / Tax ID */
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $taxId = null;

    /** Téléphone */
    #[ORM\Column(length: 30, nullable: true)]
    #[Assert\Regex(pattern: '/^[0-9\-\+\s\(\)]{8,30}$/', message: 'Le numéro de téléphone n\'est pas valide.')]
    private ?string $telephone = null;

    /** Email */
    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Email(message: 'Veuillez saisir un email valide.')]
    private ?string $email = null;

    /** Adresse postale */
    #[ORM\Column(length: 500, nullable: true)]
    private ?string $adresse = null;

    /** Ville */
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $ville = null;

    /** Remarques internes */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $remarques = null;

    /** Statut du client */
    #[ORM\Column(length: 50)]
    private ?string $statuts = self::STATUT_ACTIF;

    /** Date de création */
    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    /** Image de profil (Filename) */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    /**
     * Avocat responsable de ce client.
     * Défini automatiquement lors de la création (avocat = user connecté si AVOCAT,
     * ou choisi par l'admin).
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $avocat = null;

    /** @var Collection<int, Dossier> */
    #[ORM\OneToMany(targetEntity: Dossier::class, mappedBy: 'client', cascade: ['persist'])]
    private Collection $dossiers;

    /** @var Collection<int, Factures> */
    #[ORM\OneToMany(targetEntity: Factures::class, mappedBy: 'client', cascade: ['persist'])]
    private Collection $factures;

    /** @var Collection<int, RendezVous> */
    #[ORM\OneToMany(targetEntity: RendezVous::class, mappedBy: 'client')]
    private Collection $rendezvous;

    public function __construct()
    {
        $this->dossiers = new ArrayCollection();
        $this->factures = new ArrayCollection();
        $this->rendezvous = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        if ($this->createdAt === null) {
            $this->createdAt = new \DateTime();
        }
    }

    public function getFullName(): string
    {
        if ($this->type === self::TYPE_ENTREPRISE && $this->nomEntreprise) {
            return $this->nomEntreprise;
        }
        return trim(($this->prenom ?? '') . ' ' . ($this->nom ?? ''));
    }

    // --- Getters & Setters ---

    public function getId(): ?int { return $this->id; }

    public function getType(): ?string { return $this->type; }
    public function setType(string $type): static { $this->type = $type; return $this; }

    public function getPrenom(): ?string { return $this->prenom; }
    public function setPrenom(?string $prenom): static { $this->prenom = $prenom; return $this; }

    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): static { $this->nom = $nom; return $this; }

    public function getNomEntreprise(): ?string { return $this->nomEntreprise; }
    public function setNomEntreprise(?string $nomEntreprise): static { $this->nomEntreprise = $nomEntreprise; return $this; }

    public function getTaxId(): ?string { return $this->taxId; }
    public function setTaxId(?string $taxId): static { $this->taxId = $taxId; return $this; }

    public function getTelephone(): ?string { return $this->telephone; }
    public function setTelephone(?string $telephone): static { $this->telephone = $telephone; return $this; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(?string $email): static { $this->email = $email; return $this; }

    public function getAdresse(): ?string { return $this->adresse; }
    public function setAdresse(?string $adresse): static { $this->adresse = $adresse; return $this; }

    public function getVille(): ?string { return $this->ville; }
    public function setVille(?string $ville): static { $this->ville = $ville; return $this; }

    public function getRemarques(): ?string { return $this->remarques; }
    public function setRemarques(?string $remarques): static { $this->remarques = $remarques; return $this; }

    public function getStatuts(): ?string { return $this->statuts; }
    public function setStatuts(string $statuts): static { $this->statuts = $statuts; return $this; }

    public function getCreatedAt(): ?\DateTimeInterface { return $this->createdAt; }
    public function setCreatedAt(\DateTimeInterface $createdAt): static { $this->createdAt = $createdAt; return $this; }

    public function getImage(): ?string { return $this->image; }
    public function setImage(?string $image): static { $this->image = $image; return $this; }

    public function getAvocat(): ?User { return $this->avocat; }
    public function setAvocat(?User $avocat): static { $this->avocat = $avocat; return $this; }

    /** @return Collection<int, Dossier> */
    public function getDossiers(): Collection { return $this->dossiers; }

    public function addDossier(Dossier $dossier): static
    {
        if (!$this->dossiers->contains($dossier)) {
            $this->dossiers->add($dossier);
            $dossier->setClient($this);
        }
        return $this;
    }

    public function removeDossier(Dossier $dossier): static
    {
        if ($this->dossiers->removeElement($dossier)) {
            if ($dossier->getClient() === $this) {
                $dossier->setClient(null);
            }
        }
        return $this;
    }

    /** @return Collection<int, Factures> */
    public function getFactures(): Collection { return $this->factures; }

    public function addFacture(Factures $facture): static
    {
        if (!$this->factures->contains($facture)) {
            $this->factures->add($facture);
            $facture->setClient($this);
        }
        return $this;
    }

    public function removeFacture(Factures $facture): static
    {
        if ($this->factures->removeElement($facture)) {
            if ($facture->getClient() === $this) {
                $facture->setClient(null);
            }
        }
        return $this;
    }

    /** @return Collection<int, RendezVous> */
    public function getRendezvous(): Collection { return $this->rendezvous; }

    public function addRendezvous(RendezVous $rendezvous): static
    {
        if (!$this->rendezvous->contains($rendezvous)) {
            $this->rendezvous->add($rendezvous);
            $rendezvous->setClient($this);
        }
        return $this;
    }

    public function removeRendezvous(RendezVous $rendezvous): static
    {
        if ($this->rendezvous->removeElement($rendezvous)) {
            if ($rendezvous->getClient() === $this) {
                $rendezvous->setClient(null);
            }
        }
        return $this;
    }
}
