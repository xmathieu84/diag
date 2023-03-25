<?php

namespace App\Entity;

use App\Repository\CGUventeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CGUVenteRepository::class)
 */
class CGUvente
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $cgu;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;



    /**
     * @ORM\ManyToOne(targetEntity=Demandeur::class, inversedBy="cGUventes")
     */
    private $demnadeur;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $retract;

    /**
     * @ORM\OneToOne(targetEntity=Intervention::class, cascade={"persist"})
     */
    private $inter;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCgu(): ?bool
    {
        return $this->cgu;
    }

    public function setCgu(bool $cgu): self
    {
        $this->cgu = $cgu;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }



    public function getDemnadeur(): ?Demandeur
    {
        return $this->demnadeur;
    }

    public function setDemnadeur(?Demandeur $demnadeur): self
    {
        $this->demnadeur = $demnadeur;

        return $this;
    }

    public function getRetract(): ?bool
    {
        return $this->retract;
    }

    public function setRetract(?bool $retract): self
    {
        $this->retract = $retract;

        return $this;
    }

    public function getInter(): ?Intervention
    {
        return $this->inter;
    }

    public function setInter(?Intervention $inter): self
    {
        $this->inter = $inter;

        return $this;
    }
}
