<?php

namespace App\Entity;

use App\Repository\FamilleDiagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FamilleDiagRepository::class)
 */
class FamilleDiag
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
     * @ORM\OneToMany(targetEntity=TypeDiag::class, mappedBy="familleDiag")
     */
    private $typeDiag;

    public function __construct()
    {
        $this->typeDiag = new ArrayCollection();
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
     * @return Collection<int, TypeDiag>
     */
    public function getTypeDiag(): Collection
    {
        return $this->typeDiag;
    }

    public function addTypeDiag(TypeDiag $typeDiag): self
    {
        if (!$this->typeDiag->contains($typeDiag)) {
            $this->typeDiag[] = $typeDiag;
            $typeDiag->setFamilleDiag($this);
        }

        return $this;
    }

    public function removeTypeDiag(TypeDiag $typeDiag): self
    {
        if ($this->typeDiag->removeElement($typeDiag)) {
            // set the owning side to null (unless already changed)
            if ($typeDiag->getFamilleDiag() === $this) {
                $typeDiag->setFamilleDiag(null);
            }
        }

        return $this;
    }
}
