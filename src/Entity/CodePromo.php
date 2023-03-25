<?php

namespace App\Entity;

use App\Repository\CodePromoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CodePromoRepository::class)
 */
class CodePromo
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $dateDebut;

    /**
     * @ORM\Column(type="date")
     */
    private $dateFin;

    /**
     * @ORM\Column(type="float")
     */
    private $remise;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $codeReduc;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $profil;

    /**
     * @ORM\Column(type="boolean")
     */
    private $actif;

    /**
     * @ORM\ManyToOne(targetEntity=Abonnements::class, inversedBy="codePromos")
     */
    private $abonnementOtd;

    /**
     * @ORM\ManyToOne(targetEntity=AbonnementGci::class, inversedBy="codePromos")
     */
    private $abonnementGci;









    public function getId(): ?int
    {
        return $this->id;
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

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getRemise(): ?float
    {
        return $this->remise;
    }

    public function setRemise(float $remise): self
    {
        $this->remise = $remise;

        return $this;
    }

    public function getCodeReduc(): ?string
    {
        return $this->codeReduc;
    }

    public function setCodeReduc(string $codeReduc): self
    {
        $this->codeReduc = $codeReduc;

        return $this;
    }

    public function getProfil(): ?string
    {
        return $this->profil;
    }

    public function setProfil(string $profil): self
    {
        $this->profil = $profil;

        return $this;
    }

    public function getActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }

    public function getAbonnementOtd(): ?Abonnements
    {
        return $this->abonnementOtd;
    }

    public function setAbonnementOtd(?Abonnements $abonnementOtd): self
    {
        $this->abonnementOtd = $abonnementOtd;

        return $this;
    }

    public function getAbonnementGci(): ?AbonnementGci
    {
        return $this->abonnementGci;
    }

    public function setAbonnementGci(?AbonnementGci $abonnementGci): self
    {
        $this->abonnementGci = $abonnementGci;

        return $this;
    }





}
