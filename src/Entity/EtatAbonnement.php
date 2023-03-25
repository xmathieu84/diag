<?php

namespace App\Entity;

use App\Repository\EtatAbonnementRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EtatAbonnementRepository::class)
 */
class EtatAbonnement
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Entreprise::class, inversedBy="etatAbonnements")
     */
    private $entreprise;

    /**
     * @ORM\ManyToOne(targetEntity=Abonnements::class, inversedBy="etatAbonnements")
     */
    private $abonnement;

    /**
     * @ORM\Column(type="boolean")
     */
    private $abonne;

    /**
     * @ORM\Column(type="date_immutable")
     */
    private $dateDebut;

    /**
     * @ORM\Column(type="date_immutable")
     */
    private $datefin;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $cgu;

    /**
     * @ORM\Column(type="float")
     */
    private $montant;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lien;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $reconduction;

    public function __construct()
    {
        $this->reconduction =true;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEntreprise(): ?Entreprise
    {
        return $this->entreprise;
    }

    public function setEntreprise(?Entreprise $entreprise): self
    {
        $this->entreprise = $entreprise;

        return $this;
    }

    public function getAbonnement(): ?Abonnements
    {
        return $this->abonnement;
    }

    public function setAbonnement(?Abonnements $abonnement): self
    {
        $this->abonnement = $abonnement;

        return $this;
    }

    public function getAbonne(): ?bool
    {
        return $this->abonne;
    }

    public function setAbonne(bool $abonne): self
    {
        $this->abonne = $abonne;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDatefin(): ?\DateTimeInterface
    {
        return $this->datefin;
    }

    public function setDatefin(\DateTimeInterface $datefin): self
    {
        $this->datefin = $datefin;

        return $this;
    }

    public function getCgu(): ?bool
    {
        return $this->cgu;
    }

    public function setCgu(?bool $cgu): self
    {
        $this->cgu = $cgu;

        return $this;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getLien(): ?string
    {
        return $this->lien;
    }

    public function setLien(?string $lien): self
    {
        $this->lien = $lien;

        return $this;
    }

    public function getReconduction(): ?bool
    {
        return $this->reconduction;
    }

    public function setReconduction(?bool $reconduction): self
    {
        $this->reconduction = $reconduction;

        return $this;
    }
}
