<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\IntemperiesRepository")
 */
class Intemperies
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Assert\Type("boolean")
     * 
     */
    private $neige;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Assert\Type("boolean")
     */
    private $vent;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Assert\Type("boolean")
     */
    private $grele;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Assert\Type("boolean")
     */
    private $pluie;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Assert\Type("boolean")
     */
    private $canicule;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Assert\Type("boolean")
     */
    private $grand_froid;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Regex(pattern="/[A-Za-z0-9_'.,]/",message="Le message est invalide")
     */
    private $autre;

    /**
     * @ORM\Column(type="date", nullable=true)
     * 
     */
    private $date_intemperie;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNeige(): ?bool
    {
        return $this->neige;
    }

    public function setNeige(?bool $neige): self
    {
        $this->neige = $neige;

        return $this;
    }

    public function getVent(): ?bool
    {
        return $this->vent;
    }

    public function setVent(?bool $vent): self
    {
        $this->vent = $vent;

        return $this;
    }

    public function getGrele(): ?bool
    {
        return $this->grele;
    }

    public function setGrele(?bool $grele): self
    {
        $this->grele = $grele;

        return $this;
    }

    public function getPluie(): ?bool
    {
        return $this->pluie;
    }

    public function setPluie(?bool $pluie): self
    {
        $this->pluie = $pluie;

        return $this;
    }

    public function getCanicule(): ?bool
    {
        return $this->canicule;
    }

    public function setCanicule(?bool $canicule): self
    {
        $this->canicule = $canicule;

        return $this;
    }

    public function getGrandFroid(): ?bool
    {
        return $this->grand_froid;
    }

    public function setGrandFroid(?bool $grand_froid): self
    {
        $this->grand_froid = $grand_froid;

        return $this;
    }

    public function getAutre(): ?string
    {
        return $this->autre;
    }

    public function setAutre(?string $autre): self
    {
        $this->autre = $autre;

        return $this;
    }

    public function getDateIntemperie(): ?\DateTimeInterface
    {
        return $this->date_intemperie;
    }

    public function setDateIntemperie($date_intemperie): self
    {
        $this->date_intemperie = $date_intemperie;

        return $this;
    }


}
