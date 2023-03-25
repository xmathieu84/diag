<?php

namespace App\Entity;

use App\Repository\TauxHoraireRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity(repositoryClass=TauxHoraireRepository::class)
 */
class TauxHoraire
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=ListeInterTypeInter::class, inversedBy="tauxHoraires")
     */
    private $inter;

    /**
     * @ORM\ManyToOne(targetEntity=Salarie::class, inversedBy="tauxHoraires")
     */
    private $salarie;

    /**
     * @ORM\Column(type="float",nullable = true)
     */
    private $taux;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $prixMinimum;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInter(): ?ListeInterTypeInter
    {
        return $this->inter;
    }

    public function setInter(?ListeInterTypeInter $inter): self
    {
        $this->inter = $inter;

        return $this;
    }

    public function getSalarie(): ?Salarie
    {
        return $this->salarie;
    }

    public function setSalarie(?Salarie $salarie): self
    {
        $this->salarie = $salarie;

        return $this;
    }

    public function getTaux(): ?float
    {
        return $this->taux;
    }

    public function setTaux(float $taux): self
    {
        $this->taux = $taux;

        return $this;
    }

    public function getPrixMinimum(): ?float
    {
        return $this->prixMinimum;
    }

    public function setPrixMinimum(?float $prixMinimum): self
    {
        $this->prixMinimum = $prixMinimum;

        return $this;
    }
}
