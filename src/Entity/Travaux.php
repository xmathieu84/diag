<?php

namespace App\Entity;

use App\Repository\TravauxRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TravauxRepository::class)
 */
class Travaux
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
    private $nom;

    /**
     * @ORM\ManyToMany(targetEntity=Intervention::class, inversedBy="travauxes")
     */
    private $intervention;

    /**
     * @ORM\ManyToMany(targetEntity=ProBtp::class, mappedBy="travaux")
     */
    private $proBtps;

    public function __construct()
    {
        $this->intervention = new ArrayCollection();
        $this->proBtps = new ArrayCollection();
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

    /**
     * @return Collection|Intervention[]
     */
    public function getIntervention(): Collection
    {
        return $this->intervention;
    }

    public function addIntervention(Intervention $intervention): self
    {
        if (!$this->intervention->contains($intervention)) {
            $this->intervention[] = $intervention;
        }

        return $this;
    }

    public function removeIntervention(Intervention $intervention): self
    {
        $this->intervention->removeElement($intervention);

        return $this;
    }

    /**
     * @return Collection|ProBtp[]
     */
    public function getProBtps(): Collection
    {
        return $this->proBtps;
    }

    public function addProBtp(ProBtp $proBtp): self
    {
        if (!$this->proBtps->contains($proBtp)) {
            $this->proBtps[] = $proBtp;
            $proBtp->addTravaux($this);
        }

        return $this;
    }

    public function removeProBtp(ProBtp $proBtp): self
    {
        if ($this->proBtps->removeElement($proBtp)) {
            $proBtp->removeTravaux($this);
        }

        return $this;
    }
}
