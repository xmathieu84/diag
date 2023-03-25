<?php

namespace App\Entity;

use App\Repository\TailleBienRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TailleBienRepository::class)
 */
class TailleBien
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
    private $taille;



    /**
     * @ORM\ManyToOne(targetEntity=TypeBien::class, inversedBy="taille")
     */
    private $typeBien;

    /**
     * @ORM\OneToMany(targetEntity=PrixOdiMission::class, mappedBy="taille")
     */
    private $prixOdiMissions;

    /**
     * @ORM\ManyToMany(targetEntity=Mission::class, inversedBy="tailleBiens")
     */
    private $missionExclues;

    /**
     * @ORM\ManyToMany(targetEntity=Pack::class, inversedBy="tailleBiens")
     */
    private $packExclu;

    /**
     * @ORM\OneToMany(targetEntity=PackOdiPrixTaille::class, mappedBy="taille")
     */
    private $packOdiPrixTailles;

    /**
     * @ORM\OneToMany(targetEntity=PrixPrelevement::class, mappedBy="taille")
     */
    private $prixPrelevements;

    /**
     * @ORM\OneToMany(targetEntity=InterDiag::class, mappedBy="tailleBien")
     */
    private $interDiags;



    public function __construct()
    {
        $this->prixOdiMissions = new ArrayCollection();
        $this->missionExclues = new ArrayCollection();
        $this->packExclu = new ArrayCollection();
        $this->packOdiPrixTailles = new ArrayCollection();
        $this->prixPrelevements = new ArrayCollection();
        $this->interDiags = new ArrayCollection();

    }



    public function getId(): ?int
    {
        return $this->id;
    }


    public function getTaille(): ?string
    {
        return $this->taille;
    }

    public function setTaille(string $taille): self
    {
        $this->taille = $taille;

        return $this;
    }



    public function getTypeBien(): ?TypeBien
    {
        return $this->typeBien;
    }

    public function setTypeBien(?TypeBien $typeBien): self
    {
        $this->typeBien = $typeBien;

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
            $prixOdiMission->setTaille($this);
        }

        return $this;
    }

    public function removePrixOdiMission(PrixOdiMission $prixOdiMission): self
    {
        if ($this->prixOdiMissions->removeElement($prixOdiMission)) {
            // set the owning side to null (unless already changed)
            if ($prixOdiMission->getTaille() === $this) {
                $prixOdiMission->setTaille(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Mission>
     */
    public function getMissionExclues(): Collection
    {
        return $this->missionExclues;
    }

    public function addMissionExclue(Mission $missionExclue): self
    {
        if (!$this->missionExclues->contains($missionExclue)) {
            $this->missionExclues[] = $missionExclue;
        }

        return $this;
    }

    public function removeMissionExclue(Mission $missionExclue): self
    {
        $this->missionExclues->removeElement($missionExclue);

        return $this;
    }

    /**
     * @return Collection<int, Pack>
     */
    public function getPackExclu(): Collection
    {
        return $this->packExclu;
    }

    public function addPackExclu(Pack $packExclu): self
    {
        if (!$this->packExclu->contains($packExclu)) {
            $this->packExclu[] = $packExclu;
        }

        return $this;
    }

    public function removePackExclu(Pack $packExclu): self
    {
        $this->packExclu->removeElement($packExclu);

        return $this;
    }

    /**
     * @return Collection<int, PackOdiPrixTaille>
     */
    public function getPackOdiPrixTailles(): Collection
    {
        return $this->packOdiPrixTailles;
    }

    public function addPackOdiPrixTaille(PackOdiPrixTaille $packOdiPrixTaille): self
    {
        if (!$this->packOdiPrixTailles->contains($packOdiPrixTaille)) {
            $this->packOdiPrixTailles[] = $packOdiPrixTaille;
            $packOdiPrixTaille->setTaille($this);
        }

        return $this;
    }

    public function removePackOdiPrixTaille(PackOdiPrixTaille $packOdiPrixTaille): self
    {
        if ($this->packOdiPrixTailles->removeElement($packOdiPrixTaille)) {
            // set the owning side to null (unless already changed)
            if ($packOdiPrixTaille->getTaille() === $this) {
                $packOdiPrixTaille->setTaille(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PrixPrelevement>
     */
    public function getPrixPrelevements(): Collection
    {
        return $this->prixPrelevements;
    }

    public function addPrixPrelevement(PrixPrelevement $prixPrelevement): self
    {
        if (!$this->prixPrelevements->contains($prixPrelevement)) {
            $this->prixPrelevements[] = $prixPrelevement;
            $prixPrelevement->setTaille($this);
        }

        return $this;
    }

    public function removePrixPrelevement(PrixPrelevement $prixPrelevement): self
    {
        if ($this->prixPrelevements->removeElement($prixPrelevement)) {
            // set the owning side to null (unless already changed)
            if ($prixPrelevement->getTaille() === $this) {
                $prixPrelevement->setTaille(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, InterDiag>
     */
    public function getInterDiags(): Collection
    {
        return $this->interDiags;
    }

    public function addInterDiag(InterDiag $interDiag): self
    {
        if (!$this->interDiags->contains($interDiag)) {
            $this->interDiags[] = $interDiag;
            $interDiag->setTailleBien($this);
        }

        return $this;
    }

    public function removeInterDiag(InterDiag $interDiag): self
    {
        if ($this->interDiags->removeElement($interDiag)) {
            // set the owning side to null (unless already changed)
            if ($interDiag->getTailleBien() === $this) {
                $interDiag->setTailleBien(null);
            }
        }

        return $this;
    }


}
