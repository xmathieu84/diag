<?php

namespace App\Entity;

use App\Repository\MiltaireRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MiltaireRepository::class)
 */
class Miltaire
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
    private $nomBase;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="miltaire", cascade={"persist", "remove"})
     */
    private $user;

    /**
     * @ORM\OneToOne(targetEntity=Adresse::class, cascade={"persist", "remove"})
     */
    private $adresse;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomBase(): ?string
    {
        return $this->nomBase;
    }

    public function setNomBase(string $nomBase): self
    {
        $this->nomBase = $nomBase;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getAdresse(): ?Adresse
    {
        return $this->adresse;
    }

    public function setAdresse(?Adresse $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }


}
