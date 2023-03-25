<?php

namespace App\Entity;

use App\Repository\TypeDiagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TypeDiagRepository::class)
 */
class TypeDiag
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
     * @ORM\OneToMany(targetEntity=Mission::class, mappedBy="typeDiag")
     */
    private $mission;

    /**
     * @ORM\ManyToOne(targetEntity=FamilleDiag::class, inversedBy="typeDiag")
     */
    private $familleDiag;

    /**
     * @ORM\OneToOne(targetEntity=Prelevement::class, mappedBy="diag", cascade={"persist", "remove"})
     */
    private $prelevement;


    public function __construct()
    {
        $this->mission = new ArrayCollection();

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
     * @return Collection<int, Mission>
     */
    public function getMission(): Collection
    {
        return $this->mission;
    }

    public function addMission(Mission $mission): self
    {
        if (!$this->mission->contains($mission)) {
            $this->mission[] = $mission;
            $mission->setTypeDiag($this);
        }

        return $this;
    }

    public function removeMission(Mission $mission): self
    {
        if ($this->mission->removeElement($mission)) {
            // set the owning side to null (unless already changed)
            if ($mission->getTypeDiag() === $this) {
                $mission->setTypeDiag(null);
            }
        }

        return $this;
    }

    public function getFamilleDiag(): ?FamilleDiag
    {
        return $this->familleDiag;
    }

    public function setFamilleDiag(?FamilleDiag $familleDiag): self
    {
        $this->familleDiag = $familleDiag;

        return $this;
    }

    public function getPrelevement(): ?Prelevement
    {
        return $this->prelevement;
    }

    public function setPrelevement(?Prelevement $prelevement): self
    {
        // unset the owning side of the relation if necessary
        if ($prelevement === null && $this->prelevement !== null) {
            $this->prelevement->setDiag(null);
        }

        // set the owning side of the relation if necessary
        if ($prelevement !== null && $prelevement->getDiag() !== $this) {
            $prelevement->setDiag($this);
        }

        $this->prelevement = $prelevement;

        return $this;
    }


}
