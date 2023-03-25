<?php

namespace App\Entity;

use App\Repository\FichierInfoComplementaireRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FichierInfoComplementaireRepository::class)
 */
class FichierInfoComplementaire
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
     * @ORM\ManyToOne(targetEntity=InfoComplementaire::class, inversedBy="fichierInfoComplementaires")
     */
    private $InfoComplementaires;

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

    public function getInfoComplementaires(): ?InfoComplementaire
    {
        return $this->InfoComplementaires;
    }

    public function setInfoComplementaires(?InfoComplementaire $InfoComplementaires): self
    {
        $this->InfoComplementaires = $InfoComplementaires;

        return $this;
    }
}
