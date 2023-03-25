<?php

namespace App\Entity;

use App\Repository\RemiseExceptionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RemiseExceptionRepository::class)
 */
class RemiseException
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $debut;

    /**
     * @ORM\Column(type="date")
     */
    private $fin;

    /**
     * @ORM\Column(type="float")
     */
    private $taux;





    /**
     * @ORM\ManyToOne(targetEntity=PrixOdiMission::class, inversedBy="remiseExceptions")
     */
    private $mission;

    /**
     * @ORM\ManyToOne(targetEntity=PackOdiPrixTaille::class, inversedBy="remiseExceptions")
     */
    private $remisePack;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDebut(): ?\DateTimeInterface
    {
        return $this->debut;
    }

    public function setDebut(\DateTimeInterface $debut): self
    {
        $this->debut = $debut;

        return $this;
    }

    public function getFin(): ?\DateTimeInterface
    {
        return $this->fin;
    }

    public function setFin(\DateTimeInterface $fin): self
    {
        $this->fin = $fin;

        return $this;
    }

    public function getTaux(): ?float
    {
        return $this->taux;
    }

    public function setTaux(float $taux): self
    {
        $this->taux = $taux;

        return $this;
    }




    public function getMission(): ?PrixOdiMission
    {
        return $this->mission;
    }

    public function setMission(?PrixOdiMission $mission): self
    {
        $this->mission = $mission;

        return $this;
    }

    public function getRemisePack(): ?PackOdiPrixTaille
    {
        return $this->remisePack;
    }

    public function setRemisePack(?PackOdiPrixTaille $remisePack): self
    {
        $this->remisePack = $remisePack;

        return $this;
    }
}
