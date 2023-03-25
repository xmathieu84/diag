<?php

namespace App\Entity;

use App\Repository\DetailPrixRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DetailPrixRepository::class)
 */
class MissionOdi
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Salarie::class, inversedBy="detailPrixes")
     */
    private $odi;


    /**
     * @ORM\ManyToOne(targetEntity=Mission::class, inversedBy="detailsPrix")
     */
    private $mission;

    /**
     * @ORM\OneToMany(targetEntity=PrixOdiMission::class, mappedBy="missionOdi")
     */
    private $prixOdiMissions;



    public function __construct()
    {

        $this->remiseExceptions = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOdi(): ?Salarie
    {
        return $this->odi;
    }

    public function setOdi(?Salarie $odi): self
    {
        $this->odi = $odi;

        return $this;
    }



    public function getMission(): ?Mission
    {
        return $this->mission;
    }

    public function setMission(?Mission $mission): self
    {
        $this->mission = $mission;

        return $this;
    }

    /**
     * @return Collection<int, PrixOdiMission>
     */
    public function getPrixOdiMissions(): Collection
    {
        return $this->prixOdiMissions;
    }

    public function addPrixOdiMission(PrixOdiMission $prixOdiMission): self
    {
        if (!$this->prixOdiMissions->contains($prixOdiMission)) {
            $this->prixOdiMissions[] = $prixOdiMission;
            $prixOdiMission->setMissionOdi($this);
        }

        return $this;
    }

    public function removePrixOdiMission(PrixOdiMission $prixOdiMission): self
    {
        if ($this->prixOdiMissions->removeElement($prixOdiMission)) {
            // set the owning side to null (unless already changed)
            if ($prixOdiMission->getMissionOdi() === $this) {
                $prixOdiMission->setMissionOdi(null);
            }
        }

        return $this;
    }




}
