<?php

namespace App\Entity;

use App\Repository\FacturesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FacturesRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Factures
{
    // Statuts de facture
    const STATUS_BROUILLON   = 'Brouillon';
    const STATUS_EN_ATTENTE  = 'En attente';
    const STATUS_PAYEE       = 'Payée';
    const STATUS_IMPAYEE     = 'Impayée';
    const STATUS_ANNULEE     = 'Annulée';

    // TVA standard
    const TVA_DEFAUT = 20.0;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /** Relation vers le client */
    #[ORM\ManyToOne(inversedBy: 'factures')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Le client est obligatoire.')]
    private ?Client $client = null;

    /** Relation vers le dossier */
    #[ORM\ManyToOne(inversedBy: 'factures')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Dossier $dossier = null;

    /** Numéro de facture */
    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Le numéro de facture est obligatoire.')]
    private ?string $numeroFacture = null;

    /** Montant HT (hors taxes) */
    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    private ?string $montantHt = null;

    /** Taux de TVA en % (ex: 20 pour 20%) */
    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    #[Assert\PositiveOrZero]
    #[Assert\LessThanOrEqual(100)]
    private ?string $tva = '20.0';

    /** Montant TTC (calculé automatiquement) */
    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?string $montantTtc = null;

    /** Statut de la facture */
    #[ORM\Column(length: 50)]
    private ?string $status = self::STATUS_EN_ATTENTE;

    /** Date d'émission */
    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $dateEmission = null;

    /** Date d'échéance */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $dateEcheance = null;

    /** @var Collection<int, ArticleFacture> */
    #[ORM\OneToMany(
        targetEntity: ArticleFacture::class,
        mappedBy: 'facture',
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $articles;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
        $this->dateEmission = new \DateTime();
        $this->tva = (string) self::TVA_DEFAUT;
    }

    /**
     * Calcule automatiquement montant_ttc = montant_ht * (1 + tva/100)
     * Appelé avant persist et update
     */
    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function calculerMontantTtc(): void
    {
        if ($this->montantHt !== null && $this->tva !== null) {
            $this->montantTtc = (string) round((float) $this->montantHt * (1 + (float) $this->tva / 100), 2);
        }
    }

    /**
     * Recalcule le montant HT depuis les articles de la facture
     * (utile quand les articles sont modifiés)
     */
    public function recalculerDepuisArticles(): void
    {
        $total = 0.0;
        foreach ($this->articles as $article) {
            $total += $article->getTotal() ?? 0;
        }
        $this->montantHt = (string) round($total, 2);
        $this->calculerMontantTtc();
    }

    // --- Getters & Setters ---

    public function getId(): ?int { return $this->id; }

    public function getClient(): ?Client { return $this->client; }
    public function setClient(?Client $client): static { $this->client = $client; return $this; }

    public function getDossier(): ?Dossier { return $this->dossier; }
    public function setDossier(?Dossier $dossier): static { $this->dossier = $dossier; return $this; }

    public function getNumeroFacture(): ?string { return $this->numeroFacture; }
    public function setNumeroFacture(string $numeroFacture): static { $this->numeroFacture = $numeroFacture; return $this; }

    public function getMontantHt(): ?string { return $this->montantHt; }
    public function setMontantHt(?string $montantHt): static { $this->montantHt = $montantHt; return $this; }

    public function getTva(): ?string { return $this->tva; }
    public function setTva(?string $tva): static { $this->tva = $tva; return $this; }

    public function getMontantTtc(): ?string { return $this->montantTtc; }
    public function setMontantTtc(?string $montantTtc): static { $this->montantTtc = $montantTtc; return $this; }

    public function getStatus(): ?string { return $this->status; }
    public function setStatus(string $status): static { $this->status = $status; return $this; }

    public function getDateEmission(): ?\DateTimeInterface { return $this->dateEmission; }
    public function setDateEmission(\DateTimeInterface $dateEmission): static { $this->dateEmission = $dateEmission; return $this; }

    public function getDateEcheance(): ?\DateTimeInterface { return $this->dateEcheance; }
    public function setDateEcheance(?\DateTimeInterface $dateEcheance): static { $this->dateEcheance = $dateEcheance; return $this; }

    /** @return Collection<int, ArticleFacture> */
    public function getArticles(): Collection { return $this->articles; }

    public function addArticle(ArticleFacture $article): static
    {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
            $article->setFacture($this);
        }
        return $this;
    }

    public function removeArticle(ArticleFacture $article): static
    {
        if ($this->articles->removeElement($article)) {
            if ($article->getFacture() === $this) {
                $article->setFacture(null);
            }
        }
        return $this;
    }
}
