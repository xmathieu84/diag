<?php

namespace App\Entity;

use App\Repository\MissionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MissionRepository::class)
 */
class Mission
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
     * @ORM\OneToMany(targetEntity=MissionOdi::class, mappedBy="mission")
     */
    private $missionOdi;

    /**
     * @ORM\ManyToOne(targetEntity=TypeDiag::class, inversedBy="mission")
     */
    private $typeDiag;

    /**
     * @ORM\ManyToMany(targetEntity=Pack::class, mappedBy="missions")
     */
    private $packs;

    /**
     * @ORM\ManyToMany(targetEntity=TailleBien::class, mappedBy="missionExclues")
     */
    private $tailleBiens;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nomSimple;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $venteMaison;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $venteAppart;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $locationMaison;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $locationAppart;

    /**
     * @ORM\ManyToMany(targetEntity=InterDiag::class, mappedBy="missions")
     */
    private $interDiags;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $actif;



    public function __construct()
    {
        $this->missionOdi = new ArrayCollection();
        $this->packs = new ArrayCollection();
        $this->tailleBiens = new ArrayCollection();
        $this->interDiags = new ArrayCollection();

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
     * @return Collection<int, MissionOdi>
     */
    public function getDetailsPrix(): Collection
    {
        return $this->missionOdi;
    }

    public function addDetailsPrix(MissionOdi $detailsPrix): self
    {
        if (!$this->missionOdi->contains($detailsPrix)) {
            $this->missionOdi[] = $detailsPrix;
            $detailsPrix->setMission($this);
        }

        return $this;
    }

    public function removeDetailsPrix(MissionOdi $detailsPrix): self
    {
        if ($this->missionOdi->removeElement($detailsPrix)) {
            // set the owning side to null (unless already changed)
            if ($detailsPrix->getMission() === $this) {
                $detailsPrix->setMission(null);
            }
        }

        return $this;
    }

    public function getTypeDiag(): ?TypeDiag
    {
        return $this->typeDiag;
    }

    public function setTypeDiag(?TypeDiag $typeDiag): self
    {
        $this->typeDiag = $typeDiag;

        return $this;
    }

    /**
     * @return Collection<int, Pack>
     */
    public function getPacks(): Collection
    {
        return $this->packs;
    }

    public function addPack(Pack $pack): self
    {
        if (!$this->packs->contains($pack)) {
            $this->packs[] = $pack;
            $pack->addMission($this);
        }

        return $this;
    }

    public function removePack(Pack $pack): self
    {
        if ($this->packs->removeElement($pack)) {
            $pack->removeMission($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, TailleBien>
     */
    public function getTailleBiens(): Collection
    {
        return $this->tailleBiens;
    }

    public function addTailleBien(TailleBien $tailleBien): self
    {
        if (!$this->tailleBiens->contains($tailleBien)) {
            $this->tailleBiens[] = $tailleBien;
            $tailleBien->addMissionExclue($this);
        }

        return $this;
    }

    public function removeTailleBien(TailleBien $tailleBien): self
    {
        if ($this->tailleBiens->removeElement($tailleBien)) {
            $tailleBien->removeMissionExclue($this);
        }

        return $this;
    }

    public function getNomSimple(): ?string
    {
        return $this->nomSimple;
    }

    public function setNomSimple(?string $nomSimple): self
    {
        $this->nomSimple = $nomSimple;

        return $this;
    }

    public function getVenteMaison(): ?bool
    {
        return $this->venteMaison;
    }

    public function setVenteMaison(?bool $venteMaison): self
    {
        $this->venteMaison = $venteMaison;

        return $this;
    }

    public function getVenteAppart(): ?bool
    {
        return $this->venteAppart;
    }

    public function setVenteAppart(?bool $venteAppart): self
    {
        $this->venteAppart = $venteAppart;

        return $this;
    }

    public function getLocationMaison(): ?bool
    {
        return $this->locationMaison;
    }

    public function setLocationMaison(?bool $locationMaison): self
    {
        $this->locationMaison = $locationMaison;

        return $this;
    }

    public function getLocationAppart(): ?bool
    {
        return $this->locationAppart;
    }

    public function setLocationAppart(?bool $locationAppart): self
    {
        $this->locationAppart = $locationAppart;

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
            $interDiag->addMission($this);
        }

        return $this;
    }

    public function removeInterDiag(InterDiag $interDiag): self
    {
        if ($this->interDiags->removeElement($interDiag)) {
            $interDiag->removeMission($this);
        }

        return $this;
    }
    public function __toString(): string
    {
        return $this->id;
    }

    public function getActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(?bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }
}
