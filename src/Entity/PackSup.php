<?php

namespace App\Entity;

use App\Repository\PackSupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PackSupRepository::class)
 */
class PackSup
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
     * @ORM\Column(type="integer")
     */
    private $employe;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\OneToMany(targetEntity=PackSupAboInsti::class, mappedBy="packSup")
     */
    private $packSupAboInstis;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $cible;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $profil;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $limiteB;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $limiteH;

    public function __construct()
    {
        $this->packSupAboInstis = new ArrayCollection();

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

    public function getEmploye(): ?int
    {
        return $this->employe;
    }

    public function setEmploye(int $employe): self
    {
        $this->employe = $employe;

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
     * @return Collection|PackSupAboInsti[]
     */
    public function getPackSupAboInstis(): Collection
    {
        return $this->packSupAboInstis;
    }

    public function addPackSupAboInsti(PackSupAboInsti $packSupAboInsti): self
    {
        if (!$this->packSupAboInstis->contains($packSupAboInsti)) {
            $this->packSupAboInstis[] = $packSupAboInsti;
            $packSupAboInsti->setPackSup($this);
        }

        return $this;
    }

    public function removePackSupAboInsti(PackSupAboInsti $packSupAboInsti): self
    {
        if ($this->packSupAboInstis->removeElement($packSupAboInsti)) {
            // set the owning side to null (unless already changed)
            if ($packSupAboInsti->getPackSup() === $this) {
                $packSupAboInsti->setPackSup(null);
            }
        }

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

    public function getProfil(): ?string
    {
        return $this->profil;
    }

    public function setProfil(?string $profil): self
    {
        $this->profil = $profil;

        return $this;
    }

    public function getLimiteB(): ?int
    {
        return $this->limiteB;
    }

    public function setLimiteB(?int $limiteB): self
    {
        $this->limiteB = $limiteB;

        return $this;
    }

    public function getLimiteH(): ?int
    {
        return $this->limiteH;
    }

    public function setLimiteH(?int $limiteH): self
    {
        $this->limiteH = $limiteH;

        return $this;
    }
}
