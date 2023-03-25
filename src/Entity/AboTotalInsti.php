<?php

namespace App\Entity;

use App\Repository\AboTotalInstiRepository;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;

/**
 * @ORM\Entity(repositoryClass=AboTotalInstiRepository::class)
 */
class AboTotalInsti
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $debut;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $fin;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $total;





    /**
     * @ORM\ManyToOne(targetEntity=Demandeur::class, inversedBy="aboTotalInstis")
     */
    private $demandeur;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $abonne;

    /**
     * @ORM\OneToMany(targetEntity=PackSupAboInsti::class, mappedBy="aboInsti")
     */
    private $packSupAboInstis;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $mandatRecu;

    /**
     * @ORM\OneToOne(targetEntity=FactureInsti::class, mappedBy="abonnment", cascade={"persist", "remove"})
     */
    private $factureInsti;



    /**
     * @ORM\ManyToOne(targetEntity=AbonnementGci::class, inversedBy="aboTotalInstis")
     */
    private $abonnement;


    /**
     * @throws Exception
     */
    public function __construct()
    {

        $this->packSupAboInstis = new ArrayCollection();
        $this->debut = new DateTimeImmutable('NOW',new DateTimeZone('Europe/Paris'));
        $this->mandatRecu = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDebut(): ?\DateTimeImmutable
    {
        return $this->debut;
    }

    public function setDebut(?\DateTimeImmutable $debut): self
    {
        $this->debut = $debut;

        return $this;
    }

    public function getFin(): ?\DateTimeImmutable
    {
        return $this->fin;
    }

    public function setFin(?\DateTimeImmutable $fin): self
    {
        $this->fin = $fin;

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(?float $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getDemandeur(): ?Demandeur
    {
        return $this->demandeur;
    }

    public function setDemandeur(?Demandeur $demandeur): self
    {
        $this->demandeur = $demandeur;

        return $this;
    }

    public function getAbonne(): ?bool
    {
        return $this->abonne;
    }

    public function setAbonne(?bool $abonne): self
    {
        $this->abonne = $abonne;

        return $this;
    }

    /**
     * @return Collection|PackSupAboInsti[]
     */
    public function getPackSupAboInstis(): Collection
    {
        return $this->packSupAboInstis;
    }

    public function addPackSupAboInsti(PackSupAboInsti $packSupAboInsti): self
    {
        if (!$this->packSupAboInstis->contains($packSupAboInsti)) {
            $this->packSupAboInstis[] = $packSupAboInsti;
            $packSupAboInsti->setAboInsti($this);
        }

        return $this;
    }

    public function removePackSupAboInsti(PackSupAboInsti $packSupAboInsti): self
    {
        if ($this->packSupAboInstis->removeElement($packSupAboInsti)) {
            // set the owning side to null (unless already changed)
            if ($packSupAboInsti->getAboInsti() === $this) {
                $packSupAboInsti->setAboInsti(null);
            }
        }

        return $this;
    }

    public function getMandatRecu(): ?bool
    {
        return $this->mandatRecu;
    }

    public function setMandatRecu(?bool $mandatRecu): self
    {
        $this->mandatRecu = $mandatRecu;

        return $this;
    }

    public function getFactureInsti(): ?FactureInsti
    {
        return $this->factureInsti;
    }

    public function setFactureInsti(?FactureInsti $factureInsti): self
    {
        // unset the owning side of the relation if necessary
        if ($factureInsti === null && $this->factureInsti !== null) {
            $this->factureInsti->setAbonnment(null);
        }

        // set the owning side of the relation if necessary
        if ($factureInsti !== null && $factureInsti->getAbonnment() !== $this) {
            $factureInsti->setAbonnment($this);
        }

        $this->factureInsti = $factureInsti;

        return $this;
    }



    public function getAbonnement(): ?AbonnementGci
    {
        return $this->abonnement;
    }

    public function setAbonnement(?AbonnementGci $abonnement): self
    {
        $this->abonnement = $abonnement;

        return $this;
    }


}
