<?php

namespace App\Entity;

use App\Repository\EtatPrixOdiRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EtatPrixOdiRepository::class)
 */
class EtatPrixOdi
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $prix;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $etat;

    /**
     * @ORM\ManyToOne(targetEntity=Salarie::class, inversedBy="etatPrixOdis")
     */
    private $odi;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getOdi(): ?Salarie
    {
        return $this->odi;
    }

    public function setOdi(?Salarie $odi): self
    {
        $this->odi = $odi;

        return $this;
    }
}
