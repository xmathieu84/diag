<?php

namespace App\Entity;

use App\Repository\InfoComplementaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InfoComplementaireRepository::class)
 */
class InfoComplementaire
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $texte;

    /**
     * @ORM\OneToMany(targetEntity=FichierInfoComplementaire::class, mappedBy="InfoComplementaires")
     */
    private $fichierInfoComplementaires;

    /**
     * @ORM\ManyToOne(targetEntity=AppelOffre::class, inversedBy="infoComplementaires")
     */
    private $appelOffres;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    public function __construct()
    {
        $this->fichierInfoComplementaires = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTexte(): ?string
    {
        return $this->texte;
    }

    public function setTexte(string $texte): self
    {
        $this->texte = $texte;

        return $this;
    }

    /**
     * @return Collection|FichierInfoComplementaire[]
     */
    public function getFichierInfoComplementaires(): Collection
    {
        return $this->fichierInfoComplementaires;
    }

    public function addFichierInfoComplementaire(FichierInfoComplementaire $fichierInfoComplementaire): self
    {
        if (!$this->fichierInfoComplementaires->contains($fichierInfoComplementaire)) {
            $this->fichierInfoComplementaires[] = $fichierInfoComplementaire;
            $fichierInfoComplementaire->setInfoComplementaires($this);
        }

        return $this;
    }

    public function removeFichierInfoComplementaire(FichierInfoComplementaire $fichierInfoComplementaire): self
    {
        if ($this->fichierInfoComplementaires->removeElement($fichierInfoComplementaire)) {
            // set the owning side to null (unless already changed)
            if ($fichierInfoComplementaire->getInfoComplementaires() === $this) {
                $fichierInfoComplementaire->setInfoComplementaires(null);
            }
        }

        return $this;
    }

    public function getAppelOffres(): ?AppelOffre
    {
        return $this->appelOffres;
    }

    public function setAppelOffres(?AppelOffre $appelOffres): self
    {
        $this->appelOffres = $appelOffres;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }
}
