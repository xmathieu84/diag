<?php

namespace App\Entity;

use App\Repository\AbonnementGciRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AbonnementGciRepository::class)
 */
class AbonnementGci
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $prix;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $utlisateur;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $cible;

    /**
     * @ORM\Column(type="integer")
     */
    private $limiteH;

    /**
     * @ORM\Column(type="integer")
     */
    private $limiteB;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $profil;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\OneToMany(targetEntity=AboTotalInsti::class, mappedBy="abonnement")
     */
    private $aboTotalInstis;

    /**
     * @ORM\OneToMany(targetEntity=CodePromo::class, mappedBy="abonnementGci")
     */
    private $codePromos;

    /**
     * @ORM\OneToMany(targetEntity=Ambassadeur::class, mappedBy="abonnementGci")
     */
    private $ambassadeurs;

    /**
     * @ORM\Column(type="dateinterval", nullable=true)
     */
    private $duree;





    public function __construct()
    {
        $this->aboTotalInstis = new ArrayCollection();
        $this->codePromos = new ArrayCollection();
        $this->ambassadeurs = new ArrayCollection();

    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUtlisateur(): ?string
    {
        return $this->utlisateur;
    }

    public function setUtlisateur(string $utlisateur): self
    {
        $this->utlisateur = $utlisateur;

        return $this;
    }

    public function getCible(): ?string
    {
        return $this->cible;
    }

    public function setCible(string $cible): self
    {
        $this->cible = $cible;

        return $this;
    }

    public function getLimiteH(): ?int
    {
        return $this->limiteH;
    }

    public function setLimiteH(int $limiteH): self
    {
        $this->limiteH = $limiteH;

        return $this;
    }

    public function getLimiteB(): ?int
    {
        return $this->limiteB;
    }

    public function setLimiteB(int $limiteB): self
    {
        $this->limiteB = $limiteB;

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

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection|AboTotalInsti[]
     */
    public function getAboTotalInstis(): Collection
    {
        return $this->aboTotalInstis;
    }

    public function addAboTotalInsti(AboTotalInsti $aboTotalInsti): self
    {
        if (!$this->aboTotalInstis->contains($aboTotalInsti)) {
            $this->aboTotalInstis[] = $aboTotalInsti;
            $aboTotalInsti->setAbonnement($this);
        }

        return $this;
    }

    public function removeAboTotalInsti(AboTotalInsti $aboTotalInsti): self
    {
        if ($this->aboTotalInstis->removeElement($aboTotalInsti)) {
            // set the owning side to null (unless already changed)
            if ($aboTotalInsti->getAbonnement() === $this) {
                $aboTotalInsti->setAbonnement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CodePromo[]
     */
    public function getCodePromos(): Collection
    {
        return $this->codePromos;
    }

    public function addCodePromo(CodePromo $codePromo): self
    {
        if (!$this->codePromos->contains($codePromo)) {
            $this->codePromos[] = $codePromo;
            $codePromo->setAbonnementGci($this);
        }

        return $this;
    }

    public function removeCodePromo(CodePromo $codePromo): self
    {
        if ($this->codePromos->removeElement($codePromo)) {
            // set the owning side to null (unless already changed)
            if ($codePromo->getAbonnementGci() === $this) {
                $codePromo->setAbonnementGci(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Ambassadeur[]
     */
    public function getAmbassadeurs(): Collection
    {
        return $this->ambassadeurs;
    }

    public function addAmbassadeur(Ambassadeur $ambassadeur): self
    {
        if (!$this->ambassadeurs->contains($ambassadeur)) {
            $this->ambassadeurs[] = $ambassadeur;
            $ambassadeur->setAbonnementGci($this);
        }

        return $this;
    }

    public function removeAmbassadeur(Ambassadeur $ambassadeur): self
    {
        if ($this->ambassadeurs->removeElement($ambassadeur)) {
            // set the owning side to null (unless already changed)
            if ($ambassadeur->getAbonnementGci() === $this) {
                $ambassadeur->setAbonnementGci(null);
            }
        }

        return $this;
    }

    public function getDuree(): ?\DateInterval
    {
        return $this->duree;
    }

    public function setDuree(?\DateInterval $duree): self
    {
        $this->duree = $duree;

        return $this;
    }




}
