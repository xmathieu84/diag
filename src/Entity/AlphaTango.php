<?php

namespace App\Entity;

use App\Repository\AlphaTangoRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=AlphaTangoRepository::class)
 */
class AlphaTango
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     * @Assert\Regex(pattern="/[a-zA-Z0-9_']/",message="Identifiant invalide")
     */
    private $identifiant;

    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     * @Assert\Regex(pattern="/[a-zA-Z0-9_']/",message="Mots de passe invalide")
     */
    private $motDePasse;



    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     * @Assert\File(mimeTypes={ "application/pdf" })
     */
    private $attestationFormation;

    /**
     * @ORM\Column(type="datetime",nullable=true)
     */
    private $finValidite;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdentifiant(): ?string
    {
        return $this->identifiant;
    }

    public function setIdentifiant(string $identifiant): self
    {
        $this->identifiant = $identifiant;

        return $this;
    }

    public function getMotDePasse(): ?string
    {
        return $this->motDePasse;
    }

    public function setMotDePasse(string $motDePasse): self
    {
        $this->motDePasse = $motDePasse;

        return $this;
    }



    public function getAttestationFormation(): ?string
    {
        return $this->attestationFormation;
    }

    public function setAttestationFormation(string $attestationFormation): self
    {
        $this->attestationFormation = $attestationFormation;

        return $this;
    }

    public function getFinValidite(): ?\DateTimeInterface
    {
        return $this->finValidite;
    }

    public function setFinValidite(\DateTimeInterface $finValidite): self
    {
        $this->finValidite = $finValidite;

        return $this;
    }
}
