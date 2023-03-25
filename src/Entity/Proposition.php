<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\PropositionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity(repositoryClass=PropositionRepository::class)
 * @ApiResource (collectionOperations={},itemOperations={"get"},normalizationContext={"groups"={"prop:read"}})
 */
class Proposition
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups ({"prop:read","inter:read","demandeur:read","choixProp"})
     */
    private $id;

    /**
     * @ORM\Column(type="float",nullable=true)
     * @Assert\Regex(pattern="/[0-9.]/",message="Caractères invalide")
     * @Groups ({"prop:read","inter:read","demandeur:read"})
     */
    private $prix;



    /**
     * @ORM\ManyToOne(targetEntity=Salarie::class, inversedBy="propositions")
     */
    private $salarie;



    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups ({"prop:read","inter:read","demandeur:read"})
     */
    private $dateFin;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups ({"prop:read","inter:read"})
     */
    private $indemnite;

    /**
     * @ORM\ManyToOne(targetEntity=Intervention::class, inversedBy="propositions")
     *
     */
    private $inter;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $datePropose;

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



    public function getSalarie(): ?Salarie
    {
        return $this->salarie;
    }

    public function setSalarie(?Salarie $salarie): self
    {
        $this->salarie = $salarie;

        return $this;
    }



    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(?\DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getIndemnite(): ?float
    {
        return $this->indemnite;
    }

    public function setIndemnite(?float $indemnite): self
    {
        $this->indemnite = $indemnite;

        return $this;
    }

    public function getInter(): ?Intervention
    {
        return $this->inter;
    }

    public function setInter(?Intervention $inter): self
    {
        $this->inter = $inter;

        return $this;
    }

    public function getDatePropose(): ?\DateTimeInterface
    {
        return $this->datePropose;
    }

    public function setDatePropose(?\DateTimeInterface $datePropose): self
    {
        $this->datePropose = $datePropose;

        return $this;
    }
}
