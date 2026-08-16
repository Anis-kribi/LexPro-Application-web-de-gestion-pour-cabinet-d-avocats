<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    // Rôles disponibles
    const ROLE_ADMIN     = 'ROLE_ADMIN';
    const ROLE_AVOCAT    = 'ROLE_AVOCAT';
    const ROLE_ASSISTANT = 'ROLE_ASSISTANT';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $email = null;

    /** @var list<string> */
    #[ORM\Column]
    private array $roles = [];

    /** @var string The hashed password */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Assert\NotBlank(message: 'Le prénom est obligatoire.')]
    #[Assert\Length(min: 2, max: 100, minMessage: 'Le prénom doit faire au moins {{ limit }} caractères')]
    private ?string $prenom = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Assert\NotBlank(message: 'Le nom est obligatoire.')]
    #[Assert\Length(min: 2, max: 100, minMessage: 'Le nom doit faire au moins {{ limit }} caractères')]
    private ?string $nom = null;

    #[ORM\Column(length: 30, nullable: true)]
    #[Assert\Regex(pattern: '/^[0-9\-\+\s\(\)]{8,30}$/', message: 'Le numéro de téléphone n\'est pas valide.')]
    private ?string $telephone = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    /**
     * L'avocat responsable de cet assistant (null si pas assistant)
     * Un assistant est lié à UN SEUL avocat.
     */
    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'assistants')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $manager = null;

    /** @var Collection<int, User> Assistants rattachés à cet avocat */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'manager')]
    private Collection $assistants;

    /** @var Collection<int, Tache> */
    #[ORM\OneToMany(targetEntity: Tache::class, mappedBy: 'assigneA')]
    private Collection $taches;

    /** @var Collection<int, EntreeDeTemps> */
    #[ORM\OneToMany(targetEntity: EntreeDeTemps::class, mappedBy: 'user')]
    private Collection $entreesDeTemps;

    /** @var Collection<int, Document> */
    #[ORM\OneToMany(targetEntity: Document::class, mappedBy: 'telechargepar')]
    private Collection $documents;

    /** @var Collection<int, Notification> */
    #[ORM\OneToMany(targetEntity: Notification::class, mappedBy: 'user')]
    private Collection $notifications;

    /** Image de profil (Filename) */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    public function __construct()
    {
        $this->assistants = new ArrayCollection();
        $this->taches = new ArrayCollection();
        $this->entreesDeTemps = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        if ($this->createdAt === null) {
            $this->createdAt = new \DateTime();
        }
    }

    /** Retourne le nom complet de l'utilisateur */
    public function getFullName(): string
    {
        return trim(($this->prenom ?? '') . ' ' . ($this->nom ?? ''));
    }

    // --- Getters & Setters ---

    public function getId(): ?int { return $this->id; }

    /** Retourne vrai si l'utilisateur est un assistant */
    public function isAssistant(): bool
    {
        return in_array(self::ROLE_ASSISTANT, $this->getRoles(), true);
    }

    /** Retourne vrai si l'utilisateur est un avocat */
    public function isAvocat(): bool
    {
        return in_array(self::ROLE_AVOCAT, $this->getRoles(), true);
    }

    /** Retourne vrai si l'utilisateur est admin */
    public function isAdmin(): bool
    {
        return in_array(self::ROLE_ADMIN, $this->getRoles(), true);
    }

    public function getManager(): ?User { return $this->manager; }
    public function setManager(?User $manager): static { $this->manager = $manager; return $this; }

    /** @return Collection<int, User> */
    public function getAssistants(): Collection { return $this->assistants; }

    public function addAssistant(User $assistant): static
    {
        if (!$this->assistants->contains($assistant)) {
            $this->assistants->add($assistant);
            $assistant->setManager($this);
        }
        return $this;
    }

    public function removeAssistant(User $assistant): static
    {
        if ($this->assistants->removeElement($assistant)) {
            if ($assistant->getManager() === $this) {
                $assistant->setManager(null);
            }
        }
        return $this;
    }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): static { $this->email = $email; return $this; }

    public function getUserIdentifier(): string { return (string) $this->email; }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static { $this->roles = $roles; return $this; }

    public function getPassword(): ?string { return $this->password; }
    public function setPassword(string $password): static { $this->password = $password; return $this; }

    public function getPrenom(): ?string { return $this->prenom; }
    public function setPrenom(?string $prenom): static { $this->prenom = $prenom; return $this; }

    public function getNom(): ?string { return $this->nom; }
    public function setNom(?string $nom): static { $this->nom = $nom; return $this; }

    public function getTelephone(): ?string { return $this->telephone; }
    public function setTelephone(?string $telephone): static { $this->telephone = $telephone; return $this; }

    public function getImage(): ?string { return $this->image; }
    public function setImage(?string $image): static { $this->image = $image; return $this; }

    public function getCreatedAt(): ?\DateTimeInterface { return $this->createdAt; }
    public function setCreatedAt(\DateTimeInterface $createdAt): static { $this->createdAt = $createdAt; return $this; }

    /** @return Collection<int, Tache> */
    public function getTaches(): Collection { return $this->taches; }

    public function addTache(Tache $tache): static
    {
        if (!$this->taches->contains($tache)) {
            $this->taches->add($tache);
            $tache->setAssigneA($this);
        }
        return $this;
    }

    public function removeTache(Tache $tache): static
    {
        if ($this->taches->removeElement($tache)) {
            if ($tache->getAssigneA() === $this) {
                $tache->setAssigneA(null);
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
            $entreeDeTemps->setUser($this);
        }
        return $this;
    }

    public function removeEntreeDeTemps(EntreeDeTemps $entreeDeTemps): static
    {
        if ($this->entreesDeTemps->removeElement($entreeDeTemps)) {
            if ($entreeDeTemps->getUser() === $this) {
                $entreeDeTemps->setUser(null);
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
            $document->setTelechargepar($this);
        }
        return $this;
    }

    public function removeDocument(Document $document): static
    {
        if ($this->documents->removeElement($document)) {
            if ($document->getTelechargepar() === $this) {
                $document->setTelechargepar(null);
            }
        }
        return $this;
    }

    /** @return Collection<int, Notification> */
    public function getNotifications(): Collection { return $this->notifications; }

    public function addNotification(Notification $notification): static
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->setUser($this);
        }
        return $this;
    }

    public function removeNotification(Notification $notification): static
    {
        if ($this->notifications->removeElement($notification)) {
            if ($notification->getUser() === $this) {
                $notification->setUser(null);
            }
        }
        return $this;
    }

    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);
        return $data;
    }

    #[\Deprecated]
    public function eraseCredentials(): void {}
}
