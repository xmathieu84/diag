<?php

namespace App\Entity;

use App\Repository\PrelevementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PrelevementRepository::class)
 */
class Prelevement
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
     * @ORM\OneToOne(targetEntity=TypeDiag::class, inversedBy="prelevement", cascade={"persist", "remove"})
     */
    private $diag;

    /**
     * @ORM\OneToMany(targetEntity=PrixPrelevement::class, mappedBy="prelevement")
     */
    private $prixPrelevements;

    public function __construct()
    {
        $this->prixPrelevements = new ArrayCollection();
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

    public function getDiag(): ?TypeDiag
    {
        return $this->diag;
    }

    public function setDiag(?TypeDiag $diag): self
    {
        $this->diag = $diag;

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
            $prixPrelevement->setPrelevement($this);
        }

        return $this;
    }

    public function removePrixPrelevement(PrixPrelevement $prixPrelevement): self
    {
        if ($this->prixPrelevements->removeElement($prixPrelevement)) {
            // set the owning side to null (unless already changed)
            if ($prixPrelevement->getPrelevement() === $this) {
                $prixPrelevement->setPrelevement(null);
            }
        }

        return $this;
    }
}
