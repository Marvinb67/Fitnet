<?php

namespace App\Entity;

use App\Entity\Publication;
use App\Entity\Trait\SlugTrait;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: "Cet e-mail est déjà utiliser..!")]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use SlugTrait;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $resetToken;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Publication::class)]
    private Collection $publications;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Commentaire::class)]
    private Collection $commentaires;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Groupe::class)]
    private Collection $groupes;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Evenement::class)]
    private Collection $evenements;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: ReactionPublication::class, orphanRemoval: true)]
    private Collection $reactionPublications;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Message::class)]
    private Collection $messages;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: MessageRecu::class, orphanRemoval: true)]
    private Collection $messageRecus;

    #[ORM\ManyToMany(targetEntity: self::class)]
    #[ORM\JoinTable(name: 'user_amis')]
    private Collection $amis;

    #[ORM\ManyToMany(targetEntity: self::class, inversedBy: 'followedByUsers')]
    #[ORM\JoinTable(name: 'user_follows')]
    private Collection $followUsers;

    #[ORM\ManyToMany(targetEntity: self::class, mappedBy: 'followUsers')]
    #[ORM\JoinTable(name: 'user_follows')]
    private Collection $followedByUsers;

    #[ORM\ManyToMany(targetEntity: ProgrammationEvenement::class, mappedBy: 'inscritEvenement')]
    private Collection $programmationEvenements;

    #[ORM\ManyToMany(targetEntity: Groupe::class, mappedBy: 'adherentsGroupe')]
    private Collection $mesGroupes;

    public function __toString()
    {
        return '' .$this->getNom().' '.$this->getPrenom();
    }
    
    /**
     * faciliter la creation utilisateur
     *
     * @param string email
     * @param string password
     * @param string nom
     * @param string prenom
     * @return self
     */
    public static function create(string $email, string $nom, string $prenom): self
    {
        $user = new self();
        $user->email = $email;
        $user->nom = $nom;
        $user->prenom = $prenom;

        return $user;
    }

    public function __construct()
    {
        $this->publications = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
        $this->groupes = new ArrayCollection();
        $this->evenements = new ArrayCollection();
        $this->reactionPublications = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->messageRecus = new ArrayCollection();
        $this->amis = new ArrayCollection();
        $this->followUsers = new ArrayCollection();
        $this->followedByUsers = new ArrayCollection();
        $this->programmationEvenements = new ArrayCollection();
        $this->mesGroupes = new ArrayCollection();
    }

    #[PrePersist]
    public function prepesist()
    {
        $this->slug = str_replace(' ', '-',trim(strtolower($this->nom.' '.$this->prenom)));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

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
            $publication->setUser($this);
        }

        return $this;
    }

    public function removePublication(Publication $publication): self
    {
        if ($this->publications->removeElement($publication)) {
            // set the owning side to null (unless already changed)
            if ($publication->getUser() === $this) {
                $publication->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Commentaire>
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): self
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires->add($commentaire);
            $commentaire->setUser($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): self
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getUser() === $this) {
                $commentaire->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Groupe>
     */
    public function getGroupes(): Collection
    {
        return $this->groupes;
    }

    public function addGroupe(Groupe $groupe): self
    {
        if (!$this->groupes->contains($groupe)) {
            $this->groupes->add($groupe);
            $groupe->setUser($this);
        }

        return $this;
    }

    public function removeGroupe(Groupe $groupe): self
    {
        if ($this->groupes->removeElement($groupe)) {
            // set the owning side to null (unless already changed)
            if ($groupe->getUser() === $this) {
                $groupe->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Evenement>
     */
    public function getEvenements(): Collection
    {
        return $this->evenements;
    }

    public function addEvenement(Evenement $evenement): self
    {
        if (!$this->evenements->contains($evenement)) {
            $this->evenements->add($evenement);
            $evenement->setUser($this);
        }

        return $this;
    }

    public function removeEvenement(Evenement $evenement): self
    {
        if ($this->evenements->removeElement($evenement)) {
            // set the owning side to null (unless already changed)
            if ($evenement->getUser() === $this) {
                $evenement->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ReactionPublication>
     */
    public function getReactionPublications(): Collection
    {
        return $this->reactionPublications;
    }

    public function addReactionPublication(ReactionPublication $reactionPublication): self
    {
        if (!$this->reactionPublications->contains($reactionPublication)) {
            $this->reactionPublications->add($reactionPublication);
            $reactionPublication->setUser($this);
        }

        return $this;
    }

    public function removeReactionPublication(ReactionPublication $reactionPublication): self
    {
        if ($this->reactionPublications->removeElement($reactionPublication)) {
            // set the owning side to null (unless already changed)
            if ($reactionPublication->getUser() === $this) {
                $reactionPublication->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setUser($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getUser() === $this) {
                $message->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MessageRecu>
     */
    public function getMessageRecus(): Collection
    {
        return $this->messageRecus;
    }

    public function addMessageRecu(MessageRecu $messageRecu): self
    {
        if (!$this->messageRecus->contains($messageRecu)) {
            $this->messageRecus->add($messageRecu);
            $messageRecu->setUser($this);
        }

        return $this;
    }

    public function removeMessageRecu(MessageRecu $messageRecu): self
    {
        if ($this->messageRecus->removeElement($messageRecu)) {
            // set the owning side to null (unless already changed)
            if ($messageRecu->getUser() === $this) {
                $messageRecu->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getAmis(): Collection
    {
        return $this->amis;
    }

    public function addAmi(self$ami): self
    {
        if (!$this->amis->contains($ami)) {
            $this->amis->add($ami);
        }

        return $this;
    }

    public function removeAmi(self$ami): self
    {
        $this->amis->removeElement($ami);

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getFollowUsers(): Collection
    {
        return $this->followUsers;
    }

    public function addFollowUser(self$followUser): self
    {
        if (!$this->followUsers->contains($followUser)) {
            $this->followUsers->add($followUser);
        }

        return $this;
    }

    public function removeFollowUser(self$followUser): self
    {
        $this->followUsers->removeElement($followUser);

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getFollowedByUsers(): Collection
    {
        return $this->followedByUsers;
    }

    public function addFollowedByUser(self$followedByUser): self
    {
        if (!$this->followedByUsers->contains($followedByUser)) {
            $this->followedByUsers->add($followedByUser);
            $followedByUser->addFollowUser($this);
        }

        return $this;
    }

    public function removeFollowedByUser(self$followedByUser): self
    {
        if ($this->followedByUsers->removeElement($followedByUser)) {
            $followedByUser->removeFollowUser($this);
        }

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): self
    {
        $this->resetToken = $resetToken;

        return $this;
    }

    /**
     * @return Collection<int, ProgrammationEvenement>
     */
    public function getProgrammationEvenements(): Collection
    {
        return $this->programmationEvenements;
    }

    public function addProgrammationEvenement(ProgrammationEvenement $programmationEvenement): self
    {
        if (!$this->programmationEvenements->contains($programmationEvenement)) {
            $this->programmationEvenements->add($programmationEvenement);
            $programmationEvenement->addInscritEvenement($this);
        }

        return $this;
    }

    public function removeProgrammationEvenement(ProgrammationEvenement $programmationEvenement): self
    {
        if ($this->programmationEvenements->removeElement($programmationEvenement)) {
            $programmationEvenement->removeInscritEvenement($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Groupe>
     */
    public function getMesGroupes(): Collection
    {
        return $this->mesGroupes;
    }

    public function addMesGroupe(Groupe $mesGroupe): self
    {
        if (!$this->mesGroupes->contains($mesGroupe)) {
            $this->mesGroupes->add($mesGroupe);
            $mesGroupe->addAdherentsGroupe($this);
        }

        return $this;
    }

    public function removeMesGroupe(Groupe $mesGroupe): self
    {
        if ($this->mesGroupes->removeElement($mesGroupe)) {
            $mesGroupe->removeAdherentsGroupe($this);
        }

        return $this;
    }
}