<?php

namespace App\Entity;

use App\Repository\DemandeAccesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DemandeAccesRepository::class)
 */
class DemandeAcces
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Agent::class, inversedBy="demandeAcces")
     * @ORM\JoinColumn(nullable=false)
     */
    private $syndic;

    /**
     * @ORM\ManyToOne(targetEntity=Agent::class, inversedBy="demandeAccesSyndicat")
     */
    private $syndicat;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $statut;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSyndic(): ?Agent
    {
        return $this->syndic;
    }

    public function setSyndic(?Agent $syndic): self
    {
        $this->syndic = $syndic;

        return $this;
    }

    public function getSyndicat(): ?Agent
    {
        return $this->syndicat;
    }

    public function setSyndicat(?Agent $syndicat): self
    {
        $this->syndicat = $syndicat;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }


}
