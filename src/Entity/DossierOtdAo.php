<?php

namespace App\Entity;

use App\Repository\DossierOtdAoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DossierOtdAoRepository::class)
 */
class DossierOtdAo
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
     * @ORM\ManyToOne(targetEntity=ReponseAo::class, inversedBy="dossierOtdAos")
     */
    private $reponseAo;

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

    public function getReponseAo(): ?ReponseAo
    {
        return $this->reponseAo;
    }

    public function setReponseAo(?ReponseAo $reponseAo): self
    {
        $this->reponseAo = $reponseAo;

        return $this;
    }
}
