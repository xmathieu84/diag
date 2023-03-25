<?php

namespace App\Entity;

use App\Repository\FactureInstiRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FactureInstiRepository::class)
 */
class FactureInsti
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=AboTotalInsti::class, inversedBy="factureInsti", cascade={"persist", "remove"})
     */
    private $abonnment;

    /**
     * @ORM\OneToMany(targetEntity=PackSupAboInsti::class, mappedBy="factureInsti")
     */
    private $packSup;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity=Demandeur::class, inversedBy="factureInstis")
     */
    private $institution;

    public function __construct()
    {
        $this->packSup = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAbonnment(): ?AboTotalInsti
    {
        return $this->abonnment;
    }

    public function setAbonnment(?AboTotalInsti $abonnment): self
    {
        $this->abonnment = $abonnment;

        return $this;
    }

    /**
     * @return Collection|PackSupAboInsti[]
     */
    public function getPackSup(): Collection
    {
        return $this->packSup;
    }

    public function addPackSup(PackSupAboInsti $packSup): self
    {
        if (!$this->packSup->contains($packSup)) {
            $this->packSup[] = $packSup;
            $packSup->setFactureInsti($this);
        }

        return $this;
    }

    public function removePackSup(PackSupAboInsti $packSup): self
    {
        if ($this->packSup->removeElement($packSup)) {
            // set the owning side to null (unless already changed)
            if ($packSup->getFactureInsti() === $this) {
                $packSup->setFactureInsti(null);
            }
        }

        return $this;
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

    public function getDate(): ?\DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getInstitution(): ?demandeur
    {
        return $this->institution;
    }

    public function setInstitution(?demandeur $institution): self
    {
        $this->institution = $institution;

        return $this;
    }
}
