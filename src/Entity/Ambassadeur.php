<?php

namespace App\Entity;

use App\Repository\AmbassadeurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AmbassadeurRepository::class)
 */
class Ambassadeur
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;



    /**
     * @ORM\Column(type="string", length=255)
     */
    private $codeReduc;

    /**
     * @ORM\Column(type="date")
     */
    private $datedebut;

    /**
     * @ORM\Column(type="date")
     */
    private $datefin;

    /**
     * @ORM\Column(type="dateinterval")
     */
    private $dureeAbo;

    /**
     * @ORM\Column(type="integer")
     */
    private $maximum;

    /**
     * @ORM\ManyToOne(targetEntity=Abonnements::class, inversedBy="ambassadeurs")
     */
    private $abonnementOtd;

    /**
     * @ORM\ManyToOne(targetEntity=AbonnementGci::class, inversedBy="ambassadeurs")
     */
    private $abonnementGci;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $profil;

    /**
     * @ORM\Column(type="boolean")
     */
    private $actif;

    /**
     * @ORM\Column(type="float")
     */
    private $prix;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $commentaire;

    /**
     * @ORM\OneToMany(targetEntity=Entreprise::class, mappedBy="ambassadeur")
     */
    private $otd;

    /**
     * @ORM\OneToMany(targetEntity=Demandeur::class, mappedBy="ambassadeurInsti")
     */
    private $institution;

    /**
     * @ORM\OneToMany(targetEntity=Demandeur::class, mappedBy="ambassadeurGrandCompte")
     */
    private $grandCompte;

    public function __construct()
    {
        $this->otd = new ArrayCollection();
        $this->institution = new ArrayCollection();
        $this->grandCompte = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getCodeReduc(): ?string
    {
        return $this->codeReduc;
    }

    public function setCodeReduc(string $codeReduc): self
    {
        $this->codeReduc = $codeReduc;

        return $this;
    }

    public function getDatedebut(): ?\DateTimeInterface
    {
        return $this->datedebut;
    }

    public function setDatedebut(\DateTimeInterface $datedebut): self
    {
        $this->datedebut = $datedebut;

        return $this;
    }

    public function getDatefin(): ?\DateTimeInterface
    {
        return $this->datefin;
    }

    public function setDatefin(\DateTimeInterface $datefin): self
    {
        $this->datefin = $datefin;

        return $this;
    }

    public function getDureeAbo(): ?\DateInterval
    {
        return $this->dureeAbo;
    }

    public function setDureeAbo(\DateInterval $dureeAbo): self
    {
        $this->dureeAbo = $dureeAbo;

        return $this;
    }

    public function getMaximum(): ?int
    {
        return $this->maximum;
    }

    public function setMaximum(int $maximum): self
    {
        $this->maximum = $maximum;

        return $this;
    }

    public function getAbonnementOtd(): ?Abonnements
    {
        return $this->abonnementOtd;
    }

    public function setAbonnementOtd(?Abonnements $abonnementOtd): self
    {
        $this->abonnementOtd = $abonnementOtd;

        return $this;
    }

    public function getAbonnementGci(): ?AbonnementGci
    {
        return $this->abonnementGci;
    }

    public function setAbonnementGci(?AbonnementGci $abonnementGci): self
    {
        $this->abonnementGci = $abonnementGci;

        return $this;
    }

    public function getProfil(): ?string
    {
        return $this->profil;
    }

    public function setProfil(string $profil): self
    {
        $this->profil = $profil;

        return $this;
    }

    public function getActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): self
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    /**
     * @return Collection|Entreprise[]
     */
    public function getOtd(): Collection
    {
        return $this->otd;
    }

    public function addOtd(Entreprise $otd): self
    {
        if (!$this->otd->contains($otd)) {
            $this->otd[] = $otd;
            $otd->setAmbassadeur($this);
        }

        return $this;
    }

    public function removeOtd(Entreprise $otd): self
    {
        if ($this->otd->removeElement($otd)) {
            // set the owning side to null (unless already changed)
            if ($otd->getAmbassadeur() === $this) {
                $otd->setAmbassadeur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Demandeur[]
     */
    public function getInstitution(): Collection
    {
        return $this->institution;
    }

    public function addInstitution(Demandeur $institution): self
    {
        if (!$this->institution->contains($institution)) {
            $this->institution[] = $institution;
            $institution->setAmbassadeurInsti($this);
        }

        return $this;
    }

    public function removeInstitution(Demandeur $institution): self
    {
        if ($this->institution->removeElement($institution)) {
            // set the owning side to null (unless already changed)
            if ($institution->getAmbassadeurInsti() === $this) {
                $institution->setAmbassadeurInsti(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Demandeur[]
     */
    public function getGrandCompte(): Collection
    {
        return $this->grandCompte;
    }

    public function addGrandCompte(Demandeur $grandCompte): self
    {
        if (!$this->grandCompte->contains($grandCompte)) {
            $this->grandCompte[] = $grandCompte;
            $grandCompte->setAmbassadeurGrandCompte($this);
        }

        return $this;
    }

    public function removeGrandCompte(Demandeur $grandCompte): self
    {
        if ($this->grandCompte->removeElement($grandCompte)) {
            // set the owning side to null (unless already changed)
            if ($grandCompte->getAmbassadeurGrandCompte() === $this) {
                $grandCompte->setAmbassadeurGrandCompte(null);
            }
        }

        return $this;
    }
}
