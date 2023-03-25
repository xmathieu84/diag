<?php

namespace App\Entity;

use App\Repository\PiecesgeneraleRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PiecesgeneraleRepository::class)
 */
class Piecesgenerale
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
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fichier;

    /**
     * @ORM\ManyToOne(targetEntity=DossierGeneral::class, inversedBy="piecesgenerales")
     */
    private $dossierGeneral;

    /**
     * @ORM\Column(type="datetime",nullable=true)
     */
    private $date;

    public function __construct()
    {
        $this->date = new \DateTime('NOW',new \DateTimeZone('Europe/Paris'));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
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

    public function getDossierGeneral(): ?DossierGeneral
    {
        return $this->dossierGeneral;
    }

    public function setDossierGeneral(?DossierGeneral $dossierGeneral): self
    {
        $this->dossierGeneral = $dossierGeneral;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }
}
