<?php

namespace App\Entity;

use App\Repository\MeteoInterRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MeteoInterRepository::class)
 */
class MeteoInter
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
    private $temperature;

    /**
     * @ORM\Column(type="float")
     */
    private $vent;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nuage;

    /**
     * @ORM\OneToOne(targetEntity=Intervention::class, inversedBy="meteoInter", cascade={"persist", "remove"})
     */
    private $intervention;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTemperature(): ?float
    {
        return $this->temperature;
    }

    public function setTemperature(float $temperature): self
    {
        $this->temperature = $temperature;

        return $this;
    }

    public function getVent(): ?float
    {
        return $this->vent;
    }

    public function setVent(float $vent): self
    {
        $this->vent = $vent;

        return $this;
    }

    public function getNuage(): ?string
    {
        return $this->nuage;
    }

    public function setNuage(string $nuage): self
    {
        $this->nuage = $nuage;

        return $this;
    }

    public function getIntervention(): ?Intervention
    {
        return $this->intervention;
    }

    public function setIntervention(?Intervention $intervention): self
    {
        $this->intervention = $intervention;

        return $this;
    }
}
