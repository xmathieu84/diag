<?php

namespace App\Entity;

use App\Repository\ProBtpRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProBtpRepository::class)
 */
class ProBtp implements \JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $siteWeb;

    /**
     * @ORM\Column(type="integer")
     */
    private $distanceInter;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $bandeauPub;

    /**
     * @ORM\ManyToMany(targetEntity=Travaux::class, inversedBy="proBtps")
     */
    private $travaux;

    /**
     * @ORM\OneToOne(targetEntity=Demandeur::class, inversedBy="proBtp", cascade={"persist", "remove"})
     */
    private $demandeur;

    public function __construct()
    {
        $this->travaux = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSiteWeb(): ?string
    {
        return $this->siteWeb;
    }

    public function setSiteWeb(?string $siteWeb): self
    {
        $this->siteWeb = $siteWeb;

        return $this;
    }

    public function getDistanceInter(): ?int
    {
        return $this->distanceInter;
    }

    public function setDistanceInter(int $distanceInter): self
    {
        $this->distanceInter = $distanceInter;

        return $this;
    }

    public function getBandeauPub(): ?string
    {
        return $this->bandeauPub;
    }

    public function setBandeauPub(?string $bandeauPub): self
    {
        $this->bandeauPub = $bandeauPub;

        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize():array
    {
        return $this->toArray();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return get_object_vars($this);
    }

    /**
     * @return Collection|Travaux[]
     */
    public function getTravaux(): Collection
    {
        return $this->travaux;
    }

    public function addTravaux(Travaux $travaux): self
    {
        if (!$this->travaux->contains($travaux)) {
            $this->travaux[] = $travaux;
        }

        return $this;
    }

    public function removeTravaux(Travaux $travaux): self
    {
        $this->travaux->removeElement($travaux);

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
}
