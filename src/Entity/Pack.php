<?php

namespace App\Entity;

use App\Repository\PackRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PackRepository::class)
 */
class Pack
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;



    /**
     * @ORM\ManyToMany(targetEntity=Mission::class, inversedBy="packs")
     */
    private $missions;

    /**
     * @ORM\OneToMany(targetEntity=PackOdi::class, mappedBy="pack")
     */
    private $packOdis;

    /**
     * @ORM\ManyToMany(targetEntity=TailleBien::class, mappedBy="packExclu")
     */
    private $tailleBiens;



    public function __construct()
    {
        $this->missions = new ArrayCollection();
        $this->packOdis = new ArrayCollection();
        $this->tailleBiens = new ArrayCollection();

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

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
     * @return Collection<int, Mission>
     */
    public function getMissions(): Collection
    {
        return $this->missions;
    }

    public function addMission(Mission $mission): self
    {
        if (!$this->missions->contains($mission)) {
            $this->missions[] = $mission;
        }

        return $this;
    }

    public function removeMission(Mission $mission): self
    {
        $this->missions->removeElement($mission);

        return $this;
    }

    /**
     * @return Collection<int, PackOdi>
     */
    public function getPackOdis(): Collection
    {
        return $this->packOdis;
    }

    public function addPackOdi(PackOdi $packOdi): self
    {
        if (!$this->packOdis->contains($packOdi)) {
            $this->packOdis[] = $packOdi;
            $packOdi->setPack($this);
        }

        return $this;
    }

    public function removePackOdi(PackOdi $packOdi): self
    {
        if ($this->packOdis->removeElement($packOdi)) {
            // set the owning side to null (unless already changed)
            if ($packOdi->getPack() === $this) {
                $packOdi->setPack(null);
            }
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
            $tailleBien->addPackExclu($this);
        }

        return $this;
    }

    public function removeTailleBien(TailleBien $tailleBien): self
    {
        if ($this->tailleBiens->removeElement($tailleBien)) {
            $tailleBien->removePackExclu($this);
        }

        return $this;
    }


}
