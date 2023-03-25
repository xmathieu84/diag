<?php

namespace App\Entity;

use App\Repository\IncidentRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=IncidentRepository::class)
 */
class Incident
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $degatMateriel;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $degatCorporel;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDegatMateriel(): ?string
    {
        return $this->degatMateriel;
    }

    public function setDegatMateriel(?string $degatMateriel): self
    {
        $this->degatMateriel = $degatMateriel;

        return $this;
    }

    public function getDegatCorporel(): ?string
    {
        return $this->degatCorporel;
    }

    public function setDegatCorporel(?string $degatCorporel): self
    {
        $this->degatCorporel = $degatCorporel;

        return $this;
    }


}
