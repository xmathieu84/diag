<?php

namespace App\Entity;

use App\Repository\PackSupAboInstiRepository;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;

/**
 * @ORM\Entity(repositoryClass=PackSupAboInstiRepository::class)
 */
class PackSupAboInsti
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=AboTotalInsti::class, inversedBy="packSupAboInstis")
     */
    private $aboInsti;

    /**
     * @ORM\ManyToOne(targetEntity=PackSup::class, inversedBy="packSupAboInstis")
     */
    private $packSup;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $debut;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $fin;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $mandatRecu;

    /**
     * @ORM\ManyToOne(targetEntity=FactureInsti::class, inversedBy="packSup")
     */
    private $factureInsti;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->debut = new DateTimeImmutable('NOW',new DateTimeZone('Europe/Paris'));
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAboInsti(): ?AboTotalInsti
    {
        return $this->aboInsti;
    }

    public function setAboInsti(?AboTotalInsti $aboInsti): self
    {
        $this->aboInsti = $aboInsti;

        return $this;
    }

    public function getPackSup(): ?PackSup
    {
        return $this->packSup;
    }

    public function setPackSup(?PackSup $packSup): self
    {
        $this->packSup = $packSup;

        return $this;
    }

    public function getDebut(): ?\DateTimeImmutable
    {
        return $this->debut;
    }

    public function setDebut(?\DateTimeImmutable $debut): self
    {
        $this->debut = $debut;

        return $this;
    }

    public function getFin(): ?\DateTimeImmutable
    {
        return $this->fin;
    }

    public function setFin(?\DateTimeImmutable $fin): self
    {
        $this->fin = $fin;

        return $this;
    }

    public function getMandatRecu(): ?bool
    {
        return $this->mandatRecu;
    }

    public function setMandatRecu(?bool $mandatRecu): self
    {
        $this->mandatRecu = $mandatRecu;

        return $this;
    }

    public function getFactureInsti(): ?FactureInsti
    {
        return $this->factureInsti;
    }

    public function setFactureInsti(?FactureInsti $factureInsti): self
    {
        $this->factureInsti = $factureInsti;

        return $this;
    }


}
