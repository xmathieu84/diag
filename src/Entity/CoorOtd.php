<?php

namespace App\Entity;

use App\Repository\CoorOtdRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CoorOtdRepository::class)
 */
class CoorOtd
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
    private $lat;

    /**
     * @ORM\Column(type="float")
     */
    private $lon;

    /**
     * @ORM\OneToOne(targetEntity=FichierOTD::class, inversedBy="coorOtd", cascade={"persist", "remove"})
     */
    private $otd;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLat(): ?float
    {
        return $this->lat;
    }

    public function setLat(float $lat): self
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLon(): ?float
    {
        return $this->lon;
    }

    public function setLon(float $lon): self
    {
        $this->lon = $lon;

        return $this;
    }

    public function getOtd(): ?FichierOTD
    {
        return $this->otd;
    }

    public function setOtd(?FichierOTD $otd): self
    {
        $this->otd = $otd;

        return $this;
    }
}
