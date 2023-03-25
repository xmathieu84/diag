<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReservationRepository")
 * @ApiResource (collectionOperations={},itemOperations={})
 */
class Reservation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $debut;



    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $depart;





    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Salarie", inversedBy="reservations")
     */
    private $salarie;

    /**
     * @ORM\OneToOne(targetEntity=Intervention::class, inversedBy="reservation", cascade={"persist", "remove"},fetch="EAGER")
     */
    private $intervention;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDebut(): ?\DateTimeImmutable
    {
        return $this->debut;
    }

    public function setDebut(\DateTimeImmutable $debut): self
    {
        $this->debut = $debut;

        return $this;
    }



    public function getDepart(): ?\DateTimeImmutable
    {
        return $this->depart;
    }

    public function setDepart(?\DateTimeImmutable $depart): self
    {
        $this->depart = $depart;

        return $this;
    }

    public function getSalarie(): ?salarie
    {
        return $this->salarie;
    }

    public function setSalarie(?salarie $salarie): self
    {
        $this->salarie = $salarie;

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
