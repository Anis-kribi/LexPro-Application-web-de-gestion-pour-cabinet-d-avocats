<?php

namespace App\Entity;

use App\Repository\DossierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DossierRepository::class)]
class Dossier
{
    // Statuts possibles
    const STATUT_EN_COURS  = 'En cours';
    const STATUT_CLOTURE   = 'Clôturé';
    const STATUT_SUSPENDU  = 'Suspendu';
    const STATUT_ARCHIVE   = 'Archivé';

    // Priorités
    const PRIORITE_BASSE   = 'Basse';
    const PRIORITE_NORMALE = 'Normale';
    const PRIORITE_HAUTE   = 'Haute';
    const PRIORITE_URGENTE = 'Urgente';

    // Types de cas
    const TYPES_CAS = ['Civil', 'Pénal', 'Commercial', 'Familial', 'Travail', 'Administratif', 'Autre'];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /** Relation vers le client propriétaire du dossier */
    #[ORM\ManyToOne(inversedBy: 'dossiers')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Le client est obligatoire.')]
    private ?Client $client = null;

    /** Titre du dossier */
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le titre est obligatoire.')]
    #[Assert\Length(min: 3, max: 255, minMessage: 'Le titre doit faire au moins {{ limit }} caractères')]
    private ?string $titre = null;

    /** Numéro de référence unique */
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $numeroReference = null;

    /** Description / résumé du dossier */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    /** Type de cas (Civil, Pénal, etc.) */
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $typeCas = null;

    /** Statut du dossier */
    #[ORM\Column(length: 50)]
    private ?string $statuts = self::STATUT_EN_COURS;

    /** Priorité */
    #[ORM\Column(length: 50)]
    private ?string $priorite = self::PRIORITE_NORMALE;

    /** Date de début du dossier */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $dateDebut = null;

    /** Date de fin prévue ou réelle */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $dateFin = null;

    /** Nom du tribunal concerné */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nomTribunal = null;

    /** Nom de la partie adverse */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nomAdversaire = null;

    /** Avocat responsable du dossier (FK → User) */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $avocat = null;

    /** @var Collection<int, Tache> */
    #[ORM\OneToMany(targetEntity: Tache::class, mappedBy: 'dossier', cascade: ['persist', 'remove'])]
    private Collection $taches;

    /** @var Collection<int, Document> */
    #[ORM\OneToMany(targetEntity: Document::class, mappedBy: 'dossier', cascade: ['persist', 'remove'])]
    private Collection $documents;

    /** @var Collection<int, EntreeDeTemps> */
    #[ORM\OneToMany(targetEntity: EntreeDeTemps::class, mappedBy: 'dossier', cascade: ['persist', 'remove'])]
    private Collection $entreesDeTemps;

    /** @var Collection<int, Factures> */
    #[ORM\OneToMany(targetEntity: Factures::class, mappedBy: 'dossier', cascade: ['persist'])]
    private Collection $factures;

    public function __construct()
    {
        $this->taches = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->entreesDeTemps = new ArrayCollection();
        $this->factures = new ArrayCollection();
    }

    /** Calcule le total des heures facturables pour ce dossier */
    public function getTotalHeuresFacturables(): float
    {
        $total = 0.0;
        foreach ($this->entreesDeTemps as $entree) {
            if ($entree->isFacturable()) {
                $total += $entree->getHeures();
            }
        }
        return $total;
    }

    // --- Getters & Setters ---

    public function getId(): ?int { return $this->id; }

    public function getClient(): ?Client { return $this->client; }
    public function setClient(?Client $client): static { $this->client = $client; return $this; }

    public function getTitre(): ?string { return $this->titre; }
    public function setTitre(string $titre): static { $this->titre = $titre; return $this; }

    public function getNumeroReference(): ?string { return $this->numeroReference; }
    public function setNumeroReference(?string $numeroReference): static { $this->numeroReference = $numeroReference; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): static { $this->description = $description; return $this; }

    public function getTypeCas(): ?string { return $this->typeCas; }
    public function setTypeCas(?string $typeCas): static { $this->typeCas = $typeCas; return $this; }

    public function getStatuts(): ?string { return $this->statuts; }
    public function setStatuts(string $statuts): static { $this->statuts = $statuts; return $this; }

    public function getPriorite(): ?string { return $this->priorite; }
    public function setPriorite(string $priorite): static { $this->priorite = $priorite; return $this; }

    public function getDateDebut(): ?\DateTimeInterface { return $this->dateDebut; }
    public function setDateDebut(?\DateTimeInterface $dateDebut): static { $this->dateDebut = $dateDebut; return $this; }

    public function getDateFin(): ?\DateTimeInterface { return $this->dateFin; }
    public function setDateFin(?\DateTimeInterface $dateFin): static { $this->dateFin = $dateFin; return $this; }

    public function getNomTribunal(): ?string { return $this->nomTribunal; }
    public function setNomTribunal(?string $nomTribunal): static { $this->nomTribunal = $nomTribunal; return $this; }

    public function getNomAdversaire(): ?string { return $this->nomAdversaire; }
    public function setNomAdversaire(?string $nomAdversaire): static { $this->nomAdversaire = $nomAdversaire; return $this; }

    public function getAvocat(): ?User { return $this->avocat; }
    public function setAvocat(?User $avocat): static { $this->avocat = $avocat; return $this; }

    /** @return Collection<int, Tache> */
    public function getTaches(): Collection { return $this->taches; }

    public function addTache(Tache $tache): static
    {
        if (!$this->taches->contains($tache)) {
            $this->taches->add($tache);
            $tache->setDossier($this);
        }
        return $this;
    }

    public function removeTache(Tache $tache): static
    {
        if ($this->taches->removeElement($tache)) {
            if ($tache->getDossier() === $this) {
                $tache->setDossier(null);
            }
        }
        return $this;
    }

    /** @return Collection<int, Document> */
    public function getDocuments(): Collection { return $this->documents; }

    public function addDocument(Document $document): static
    {
        if (!$this->documents->contains($document)) {
            $this->documents->add($document);
            $document->setDossier($this);
        }
        return $this;
    }

    public function removeDocument(Document $document): static
    {
        if ($this->documents->removeElement($document)) {
            if ($document->getDossier() === $this) {
                $document->setDossier(null);
            }
        }
        return $this;
    }

    /** @return Collection<int, EntreeDeTemps> */
    public function getEntreesDeTemps(): Collection { return $this->entreesDeTemps; }

    public function addEntreeDeTemps(EntreeDeTemps $entreeDeTemps): static
    {
        if (!$this->entreesDeTemps->contains($entreeDeTemps)) {
            $this->entreesDeTemps->add($entreeDeTemps);
            $entreeDeTemps->setDossier($this);
        }
        return $this;
    }

    public function removeEntreeDeTemps(EntreeDeTemps $entreeDeTemps): static
    {
        if ($this->entreesDeTemps->removeElement($entreeDeTemps)) {
            if ($entreeDeTemps->getDossier() === $this) {
                $entreeDeTemps->setDossier(null);
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
            $facture->setDossier($this);
        }
        return $this;
    }

    public function removeFacture(Factures $facture): static
    {
        if ($this->factures->removeElement($facture)) {
            if ($facture->getDossier() === $this) {
                $facture->setDossier(null);
            }
        }
        return $this;
    }
}
