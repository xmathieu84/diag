<?php

namespace App\Entity;

use App\Repository\TypeBienRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TypeBienRepository::class)
 */
class TypeBien
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
     * @ORM\OneToMany(targetEntity=TailleBien::class, mappedBy="typeBien")
     */
    private $taille;

    public function __construct()
    {
        $this->taille = new ArrayCollection();
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
     * @return Collection<int, TailleBien>
     */
    public function getTaille(): Collection
    {
        return $this->taille;
    }

    public function addTaille(TailleBien $taille): self
    {
        if (!$this->taille->contains($taille)) {
            $this->taille[] = $taille;
            $taille->setTypeBien($this);
        }

        return $this;
    }

    public function removeTaille(TailleBien $taille): self
    {
        if ($this->taille->removeElement($taille)) {
            // set the owning side to null (unless already changed)
            if ($taille->getTypeBien() === $this) {
                $taille->setTypeBien(null);
            }
        }

        return $this;
    }
}
