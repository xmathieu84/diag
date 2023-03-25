<?php

namespace App\Entity;

use App\Repository\PrixPrelevementRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PrixPrelevementRepository::class)
 */
class PrixPrelevement
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $prix;

    /**
     * @ORM\ManyToOne(targetEntity=Salarie::class, inversedBy="prixPrelevements")
     */
    private $odi;

    /**
     * @ORM\ManyToOne(targetEntity=Prelevement::class, inversedBy="prixPrelevements")
     */
    private $prelevement;

    /**
     * @ORM\ManyToOne(targetEntity=TailleBien::class, inversedBy="prixPrelevements")
     */
    private $taille;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getOdi(): ?Salarie
    {
        return $this->odi;
    }

    public function setOdi(?Salarie $odi): self
    {
        $this->odi = $odi;

        return $this;
    }

    public function getPrelevement(): ?Prelevement
    {
        return $this->prelevement;
    }

    public function setPrelevement(?Prelevement $prelevement): self
    {
        $this->prelevement = $prelevement;

        return $this;
    }

    public function getTaille(): ?TailleBien
    {
        return $this->taille;
    }

    public function setTaille(?TailleBien $taille): self
    {
        $this->taille = $taille;

        return $this;
    }
}
