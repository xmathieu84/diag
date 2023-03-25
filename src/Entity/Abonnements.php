<?php

namespace App\Entity;

use App\Repository\AbonnementsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AbonnementsRepository::class)
 */
class Abonnements
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="float")
     */
    private $prix;



    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $particularites = [];

    /**
     * @ORM\OneToMany(targetEntity=EtatAbonnement::class, mappedBy="abonnement")
     */
    private $etatAbonnements;

    /**
     * @ORM\Column(type="integer")
     */
    private $otdMax;

    /**
     * @ORM\Column(type="float")
     */
    private $otdSup;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $cible;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $cerfaMax;

    /**
     * @ORM\OneToMany(targetEntity=CodePromo::class, mappedBy="abonnementOtd")
     */
    private $codePromos;

    /**
     * @ORM\OneToMany(targetEntity=Ambassadeur::class, mappedBy="abonnementOtd")
     */
    private $ambassadeurs;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $commission;

    /**
     * @ORM\Column(type="dateinterval", nullable=true)
     */
    private $duree;







    public function __construct()
    {
        $this->entreprise = new ArrayCollection();
        $this->etatAbonnements = new ArrayCollection();
        $this->codePromos = new ArrayCollection();
        $this->ambassadeurs = new ArrayCollection();


    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }




    public function getParticularites(): ?array
    {
        return $this->particularites;
    }

    public function setParticularites(?array $particularites): self
    {
        $this->particularites = $particularites;

        return $this;
    }

    /**
     * @return Collection|EtatAbonnement[]
     */
    public function getEtatAbonnements(): Collection
    {
        return $this->etatAbonnements;
    }

    public function addEtatAbonnement(EtatAbonnement $etatAbonnement): self
    {
        if (!$this->etatAbonnements->contains($etatAbonnement)) {
            $this->etatAbonnements[] = $etatAbonnement;
            $etatAbonnement->setAbonnement($this);
        }

        return $this;
    }

    public function removeEtatAbonnement(EtatAbonnement $etatAbonnement): self
    {
        if ($this->etatAbonnements->contains($etatAbonnement)) {
            $this->etatAbonnements->removeElement($etatAbonnement);
            // set the owning side to null (unless already changed)
            if ($etatAbonnement->getAbonnement() === $this) {
                $etatAbonnement->setAbonnement(null);
            }
        }

        return $this;
    }

    public function getOtdMax(): ?int
    {
        return $this->otdMax;
    }

    public function setOtdMax(int $otdMax): self
    {
        $this->otdMax = $otdMax;

        return $this;
    }

    public function getOtdSup(): ?float
    {
        return $this->otdSup;
    }

    public function setOtdSup(float $otdSup): self
    {
        $this->otdSup = $otdSup;

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

    public function getCerfaMax(): ?int
    {
        return $this->cerfaMax;
    }

    public function setCerfaMax(?int $cerfaMax): self
    {
        $this->cerfaMax = $cerfaMax;

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
            $codePromo->setAbonnementOtd($this);
        }

        return $this;
    }

    public function removeCodePromo(CodePromo $codePromo): self
    {
        if ($this->codePromos->removeElement($codePromo)) {
            // set the owning side to null (unless already changed)
            if ($codePromo->getAbonnementOtd() === $this) {
                $codePromo->setAbonnementOtd(null);
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
            $ambassadeur->setAbonnementOtd($this);
        }

        return $this;
    }

    public function removeAmbassadeur(Ambassadeur $ambassadeur): self
    {
        if ($this->ambassadeurs->removeElement($ambassadeur)) {
            // set the owning side to null (unless already changed)
            if ($ambassadeur->getAbonnementOtd() === $this) {
                $ambassadeur->setAbonnementOtd(null);
            }
        }

        return $this;
    }

    public function getCommission(): ?int
    {
        return $this->commission;
    }

    public function setCommission(?int $commission): self
    {
        $this->commission = $commission;

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
