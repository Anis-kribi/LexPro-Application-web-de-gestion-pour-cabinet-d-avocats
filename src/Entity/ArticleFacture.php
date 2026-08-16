<?php

namespace App\Entity;

use App\Repository\ArticleFactureRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ArticleFactureRepository::class)]
#[ORM\HasLifecycleCallbacks]
class ArticleFacture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /** Facture parente */
    #[ORM\ManyToOne(inversedBy: 'articles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Factures $facture = null;

    /** Description de la prestation / ligne */
    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: 'La description est obligatoire.')]
    private ?string $description = null;

    /** Quantité */
    #[ORM\Column(type: 'decimal', precision: 8, scale: 2)]
    #[Assert\Positive(message: 'La quantité doit être positive.')]
    private ?string $quantite = '1.0';

    /** Prix unitaire HT */
    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Assert\PositiveOrZero]
    private ?string $prixUnitaire = null;

    /** Total ligne = quantite * prix_unitaire (calculé auto) */
    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?string $total = null;

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function calculerTotal(): void
    {
        if ($this->quantite !== null && $this->prixUnitaire !== null) {
            $this->total = (string) round((float) $this->quantite * (float) $this->prixUnitaire, 2);
        }
    }

    // --- Getters & Setters ---

    public function getId(): ?int { return $this->id; }

    public function getFacture(): ?Factures { return $this->facture; }
    public function setFacture(?Factures $facture): static { $this->facture = $facture; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(string $description): static { $this->description = $description; return $this; }

    public function getQuantite(): ?string { return $this->quantite; }
    public function setQuantite(string $quantite): static
    {
        $this->quantite = $quantite;
        $this->calculerTotal();
        return $this;
    }

    public function getPrixUnitaire(): ?string { return $this->prixUnitaire; }
    public function setPrixUnitaire(string $prixUnitaire): static
    {
        $this->prixUnitaire = $prixUnitaire;
        $this->calculerTotal();
        return $this;
    }

    public function getTotal(): ?string { return $this->total; }
    public function setTotal(?string $total): static { $this->total = $total; return $this; }
}
