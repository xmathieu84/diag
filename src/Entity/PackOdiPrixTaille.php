<?php

namespace App\Entity;

use App\Repository\PackOdiPrixTailleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PackOdiPrixTailleRepository::class)
 */
class PackOdiPrixTaille
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float",nullable=true)
     */
    private $prix;

    /**
     * @ORM\ManyToOne(targetEntity=TailleBien::class, inversedBy="packOdiPrixTailles")
     */
    private $taille;

    /**
     * @ORM\ManyToOne(targetEntity=PackOdi::class, inversedBy="packOdiPrixTailles")
     */
    private $packOdi;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isValid;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $temps;

    /**
     * @ORM\OneToMany(targetEntity=RemiseException::class, mappedBy="remisePack")
     */
    private $remiseExceptions;

    /**
     * @ORM\OneToMany(targetEntity=InterDiag::class, mappedBy="pack")
     */
    private $interDiags;

    public function __construct()
    {
        $this->remiseExceptions = new ArrayCollection();
        $this->interDiags = new ArrayCollection();
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

    public function getPackOdi(): ?PackOdi
    {
        return $this->packOdi;
    }

    public function setPackOdi(?PackOdi $packOdi): self
    {
        $this->packOdi = $packOdi;

        return $this;
    }

    public function getIsValid(): ?bool
    {
        return $this->isValid;
    }

    public function setIsValid(?bool $isValid): self
    {
        $this->isValid = $isValid;

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
            $remiseException->setRemisePack($this);
        }

        return $this;
    }

    public function removeRemiseException(RemiseException $remiseException): self
    {
        if ($this->remiseExceptions->removeElement($remiseException)) {
            // set the owning side to null (unless already changed)
            if ($remiseException->getRemisePack() === $this) {
                $remiseException->setRemisePack(null);
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
            $interDiag->setPack($this);
        }

        return $this;
    }

    public function removeInterDiag(InterDiag $interDiag): self
    {
        if ($this->interDiags->removeElement($interDiag)) {
            // set the owning side to null (unless already changed)
            if ($interDiag->getPack() === $this) {
                $interDiag->setPack(null);
            }
        }

        return $this;
    }
}
