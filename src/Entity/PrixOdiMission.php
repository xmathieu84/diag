<?php

namespace App\Entity;

use App\Repository\PrixOdiMissionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PrixOdiMissionRepository::class)
 */
class PrixOdiMission
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $prix;

    /**
     * @ORM\ManyToOne(targetEntity=TailleBien::class, inversedBy="prixOdiMissions")
     */
    private $taille;

    /**
     * @ORM\ManyToOne(targetEntity=MissionOdi::class, inversedBy="prixOdiMissions")
     */
    private $missionOdi;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isValide;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $temps;

    /**
     * @ORM\OneToMany(targetEntity=RemiseException::class, mappedBy="mission")
     */
    private $remiseExceptions;

    public function __construct()
    {
        $this->remiseExceptions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(?float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getTaille(): ?TailleBien
    {
        return $this->taille;
    }

    public function setTaille(?TailleBien $taille): self
    {
        $this->taille = $taille;

        return $this;
    }

    public function getMissionOdi(): ?MissionOdi
    {
        return $this->missionOdi;
    }

    public function setMissionOdi(?MissionOdi $missionOdi): self
    {
        $this->missionOdi = $missionOdi;

        return $this;
    }

    public function getIsValide(): ?bool
    {
        return $this->isValide;
    }

    public function setIsValide(?bool $isValide): self
    {
        $this->isValide = $isValide;

        return $this;
    }

    public function getTemps(): ?int
    {
        return $this->temps;
    }

    public function setTemps(?int $temps): self
    {
        $this->temps = $temps;

        return $this;
    }

    /**
     * @return Collection<int, RemiseException>
     */
    public function getRemiseExceptions(): Collection
    {
        return $this->remiseExceptions;
    }

    public function addRemiseException(RemiseException $remiseException): self
    {
        if (!$this->remiseExceptions->contains($remiseException)) {
            $this->remiseExceptions[] = $remiseException;
            $remiseException->setMission($this);
        }

        return $this;
    }

    public function removeRemiseException(RemiseException $remiseException): self
    {
        if ($this->remiseExceptions->removeElement($remiseException)) {
            // set the owning side to null (unless already changed)
            if ($remiseException->getMission() === $this) {
                $remiseException->setMission(null);
            }
        }

        return $this;
    }
}
