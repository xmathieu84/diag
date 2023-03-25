<?php

namespace App\Entity;

use App\Repository\DonneesGeneralesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DonneesGeneralesRepository::class)
 */
class DonneesGenerales
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $presentation;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $information;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $intervenant;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $finance;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $juridique;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $complement;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPresentation(): ?string
    {
        return $this->presentation;
    }

    public function setPresentation(?string $presentation): self
    {
        $this->presentation = $presentation;

        return $this;
    }

    public function getInformation(): ?string
    {
        return $this->information;
    }

    public function setInformation(?string $information): self
    {
        $this->information = $information;

        return $this;
    }

    public function getIntervenant(): ?string
    {
        return $this->intervenant;
    }

    public function setIntervenant(?string $intervenant): self
    {
        $this->intervenant = $intervenant;

        return $this;
    }

    public function getFinance(): ?string
    {
        return $this->finance;
    }

    public function setFinance(?string $finance): self
    {
        $this->finance = $finance;

        return $this;
    }

    public function getJuridique(): ?string
    {
        return $this->juridique;
    }

    public function setJuridique(?string $juridique): self
    {
        $this->juridique = $juridique;

        return $this;
    }

    public function getComplement(): ?string
    {
        return $this->complement;
    }

    public function setComplement(?string $complement): self
    {
        $this->complement = $complement;

        return $this;
    }
}
