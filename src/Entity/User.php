<?php

namespace App\Entity;



use DateTime;
use DateTimeZone;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 *
 */
class User implements UserInterface, EquatableInterface,PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180,unique=true)
     * @Groups({"user:read", "user:write","demandeur:read", "demandeur:write"})
     *
     */
    private $email;
    /**
     * @ORM\Column(type="json")
     *
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\Length(min = 8, minMessage = "Votre mot de passe doit faire plus de 8 caractères")
     * @Assert\Regex(pattern="/[A-Za-z0-9_'.,]/",message="Le mot de passe contient des caractères non autorisé")
     */
    private $password;





    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     */
    private $codeActivation;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     */
    private $resetPassword;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     */
    private $dateInscription;

    /**
     * @ORM\OneToMany(targetEntity=Paiement::class, mappedBy="utilisateur")
     *
     */
    private $paiements;

    /**
     * @ORM\OneToOne(targetEntity=Miltaire::class, mappedBy="user", cascade={"persist", "remove"})
     *
     */
    private $miltaire;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mangoPayID;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $walletMangoID;

    /**
     * @ORM\Column(type="string", length=255,nullable =true)
     */
    private $bankMangoPay;

    /**
     * @ORM\Column(type="string", length=255,nullable = true)
     */
    private $cardId;
    /**
     * @ORM\OneToOne(targetEntity=Agent::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private $agent;

    /**
     *
     * @Groups ({"demandeur:write"})
     * @SerializedName("password")
     */
    private  $plainPaswword;

    /**
     * @ORM\OneToOne(targetEntity=Demandeur::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private $demandeur;

    /**
     * @ORM\OneToOne(targetEntity=Salarie::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private $salarie;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $active;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isConnect;









    public function __construct()
    {
        $this->paiements = new ArrayCollection();
        $this->dateInscription = new DateTime('NOW', new DateTimeZone('Europe/Paris'));
        $this->questionChats = new ArrayCollection();

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
    public function getUsername(): string
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
    public function addRole($role)
    {
        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function hasRole($role)
    {
        if (in_array($role, $this->roles, true)) {
            return true;
        }

        return false;
    }

    public function removeRole($role)
    {
        if ($this->hasRole($role)) {
                unset($this->roles[array_search($role, $this->roles, true)]);
        }
        $this->roles = array_values($this->roles);
        return $this;
    }



    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSalt():?string
    {
       return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getCodeActivation(): ?string
    {
        return $this->codeActivation;
    }

    public function setCodeActivation(?string $codeActivation): self
    {
        $this->codeActivation = $codeActivation;

        return $this;
    }

    public function getResetPassword(): ?string
    {
        return $this->resetPassword;
    }

    public function setResetPassword(?string $resetPassword): self
    {
        $this->resetPassword = $resetPassword;

        return $this;
    }





    public function getDateInscription(): ?\DateTimeInterface
    {
        return $this->dateInscription;
    }

    public function setDateInscription(?\DateTimeInterface $dateInscription): self
    {
        $this->dateInscription = $dateInscription;

        return $this;
    }

    /**
     * @param UserInterface $user
     * @return bool
     */
    public function isEqualTo(UserInterface $user):bool
    {
        if ($this->id !== $user->getId()) {
            return false;
        }

        if ($this->password !== $user->getPassword()) {
            return false;
        }

        if ($this->email !== $user->getEmail()) {
            return false;
        }

        return true;
    }

    /**
     * @return Collection|Paiement[]
     */
    public function getPaiements(): Collection
    {
        return $this->paiements;
    }

    public function addPaiement(Paiement $paiement): self
    {
        if (!$this->paiements->contains($paiement)) {
            $this->paiements[] = $paiement;
            $paiement->setUtilisateur($this);
        }

        return $this;
    }

    public function removePaiement(Paiement $paiement): self
    {
        if ($this->paiements->contains($paiement)) {
            $this->paiements->removeElement($paiement);
            // set the owning side to null (unless already changed)
            if ($paiement->getUtilisateur() === $this) {
                $paiement->setUtilisateur(null);
            }
        }

        return $this;
    }

    public function getMiltaire(): ?Miltaire
    {
        return $this->miltaire;
    }

    public function setMiltaire(?Miltaire $miltaire): self
    {
        $this->miltaire = $miltaire;

        // set (or unset) the owning side of the relation if necessary
        $newUser = null === $miltaire ? null : $this;
        if ($miltaire->getUser() !== $newUser) {
            $miltaire->setUser($newUser);
        }

        return $this;
    }


    public function getMangoPayID(): ?string
    {
        return $this->mangoPayID;
    }

    public function setMangoPayID(?string $mangoPayID): self
    {
        $this->mangoPayID = $mangoPayID;

        return $this;
    }

    public function getWalletMangoID(): ?string
    {
        return $this->walletMangoID;
    }

    public function setWalletMangoID(?string $walletMangoID): self
    {
        $this->walletMangoID = $walletMangoID;

        return $this;
    }

    public function getBankMangoPay(): ?string
    {
        return $this->bankMangoPay;
    }

    public function setBankMangoPay(string $bankMangoPay): self
    {
        $this->bankMangoPay = $bankMangoPay;

        return $this;
    }

    public function getCardId(): ?string
    {
        return $this->cardId;
    }

    public function setCardId(string $cardId): self
    {
        $this->cardId = $cardId;

        return $this;
    }





    public function getAgent(): ?Agent
    {
        return $this->agent;
    }

    public function setAgent(?Agent $agent): self
    {
        // unset the owning side of the relation if necessary
        if ($agent === null && $this->agent !== null) {
            $this->agent->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($agent !== null && $agent->getUser() !== $this) {
            $agent->setUser($this);
        }

        $this->agent = $agent;

        return $this;
    }
    /**
     *
     */
    public function getPlainPaswword()
    {
        return $this->plainPaswword;
    }

    public function setPlainPaswword(string $plainPaswword){
        $this->plainPaswword= $plainPaswword;

        return $this;
    }

    public function getDemandeur(): ?Demandeur
    {
        return $this->demandeur;
    }

    public function setDemandeur(?Demandeur $demandeur): self
    {
        // unset the owning side of the relation if necessary
        if ($demandeur === null && $this->demandeur !== null) {
            $this->demandeur->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($demandeur !== null && $demandeur->getUser() !== $this) {
            $demandeur->setUser($this);
        }

        $this->demandeur = $demandeur;

        return $this;
    }

    public function getSalarie(): ?Salarie
    {
        return $this->salarie;
    }

    public function setSalarie(?Salarie $salarie): self
    {
        // unset the owning side of the relation if necessary
        if ($salarie === null && $this->salarie !== null) {
            $this->salarie->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($salarie !== null && $salarie->getUser() !== $this) {
            $salarie->setUser($this);
        }

        $this->salarie = $salarie;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(?bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getIsConnect(): ?bool
    {
        return $this->isConnect;
    }

    public function setIsConnect(?bool $isConnect): self
    {
        $this->isConnect = $isConnect;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }


}
