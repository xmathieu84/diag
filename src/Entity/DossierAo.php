<?php

namespace App\Entity;

use App\Repository\DossierAoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DossierAoRepository::class)
 */
class DossierAo
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
    private $fichier;

    /**
     * @ORM\ManyToOne(targetEntity=AppelOffre::class, inversedBy="dossierAos")
     */
    private $AppelOffre;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFichier(): ?string
    {
        return $this->fichier;
    }

    public function setFichier(string $fichier): self
    {
        $this->fichier = $fichier;

        return $this;
    }

    public function getAppelOffre(): ?AppelOffre
    {
        return $this->AppelOffre;
    }

    public function setAppelOffre(?AppelOffre $AppelOffre): self
    {
        $this->AppelOffre = $AppelOffre;

        return $this;
    }
}
