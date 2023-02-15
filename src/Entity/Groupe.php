<?php

namespace App\Entity;

use App\Entity\Trait\SlugTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Trait\EditedAtTrait;
use Doctrine\ORM\Mapping\PreUpdate;
use App\Entity\Trait\CreatedAtTrait;
use App\Repository\GroupeRepository;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;

#[HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: GroupeRepository::class)]
class Groupe
{
    use CreatedAtTrait;
    use EditedAtTrait;
    use SlugTrait;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $intitule = null;

    #[ORM\ManyToOne(inversedBy: 'groupes')]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\JoinTable(name: 'adherent_group')]
    private ?User $user = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'mesGroupes')]
    private Collection $adherentsGroupe;

    #[ORM\OneToMany(mappedBy: 'groupe', targetEntity: Publication::class)]
    private Collection $publications;

    public function __construct()
    {
        $this->adherentsGroupe = new ArrayCollection();
        $this->publications = new ArrayCollection();
    }
    
    #[PrePersist]
    public function prepesist()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->editedAt = new \DateTimeImmutable();
    }

    #[PreUpdate]
    public function prepUp()
    {
        $this->editedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIntitule(): ?string
    {
        return $this->intitule;
    }

    public function setIntitule(string $intitule): self
    {
        $this->intitule = $intitule;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getAdherentsGroupe(): Collection
    {
        return $this->adherentsGroupe;
    }

    public function addAdherentsGroupe(User $adherentsGroupe): self
    {
        if (!$this->adherentsGroupe->contains($adherentsGroupe)) {
            $this->adherentsGroupe->add($adherentsGroupe);
        }

        return $this;
    }

    public function removeAdherentsGroupe(User $adherentsGroupe): self
    {
        $this->adherentsGroupe->removeElement($adherentsGroupe);

        return $this;
    }

    /**
     * @return Collection<int, Publication>
     */
    public function getPublications(): Collection
    {
        return $this->publications;
    }

    public function addPublication(Publication $publication): self
    {
        if (!$this->publications->contains($publication)) {
            $this->publications->add($publication);
            $publication->setGroupe($this);
        }

        return $this;
    }

    public function removePublication(Publication $publication): self
    {
        if ($this->publications->removeElement($publication)) {
            // set the owning side to null (unless already changed)
            if ($publication->getGroupe() === $this) {
                $publication->setGroupe(null);
            }
        }

        return $this;
    }
}
