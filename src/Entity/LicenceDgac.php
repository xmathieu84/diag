<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LicenceDgacRepository")
 */
class LicenceDgac
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     */
    private $numeroDeLicence;

    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     * @Assert\File(mimeTypes={ "application/pdf" })
     */
    private $fichierLicence;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $exploitant;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroDeLicence(): ?string
    {
        return $this->numeroDeLicence;
    }

    public function setNumeroDeLicence(string $numeroDeLicence): self
    {
        $this->numeroDeLicence = $numeroDeLicence;

        return $this;
    }

    public function getFichierLicence(): ?string
    {
        return $this->fichierLicence;
    }

    public function setFichierLicence(string $fichierLicence): self
    {
        $this->fichierLicence = $fichierLicence;

        return $this;
    }

    public function getExploitant(): ?string
    {
        return $this->exploitant;
    }

    public function setExploitant(?string $exploitant): self
    {
        $this->exploitant = $exploitant;

        return $this;
    }


}
