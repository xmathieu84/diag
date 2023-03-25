<?php

namespace App\Entity;

use App\Repository\PackOdiRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PackOdiRepository::class)
 */
class PackOdi
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Salarie::class, inversedBy="packOdis")
     */
    private $odi;

    /**
     * @ORM\ManyToOne(targetEntity=Pack::class, inversedBy="packOdis")
     */
    private $pack;

    /**
     * @ORM\OneToMany(targetEntity=PackOdiPrixTaille::class, mappedBy="packOdi")
     */
    private $packOdiPrixTailles;



    public function __construct()
    {
        $this->packOdiPrixTailles = new ArrayCollection();
        ;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPack(): ?Pack
    {
        return $this->pack;
    }

    public function setPack(?Pack $pack): self
    {
        $this->pack = $pack;

        return $this;
    }

    /**
     * @return Collection<int, PackOdiPrixTaille>
     */
    public function getPackOdiPrixTailles(): Collection
    {
        return $this->packOdiPrixTailles;
    }

    public function addPackOdiPrixTaille(PackOdiPrixTaille $packOdiPrixTaille): self
    {
        if (!$this->packOdiPrixTailles->contains($packOdiPrixTaille)) {
            $this->packOdiPrixTailles[] = $packOdiPrixTaille;
            $packOdiPrixTaille->setPackOdi($this);
        }

        return $this;
    }

    public function removePackOdiPrixTaille(PackOdiPrixTaille $packOdiPrixTaille): self
    {
        if ($this->packOdiPrixTailles->removeElement($packOdiPrixTaille)) {
            // set the owning side to null (unless already changed)
            if ($packOdiPrixTaille->getPackOdi() === $this) {
                $packOdiPrixTaille->setPackOdi(null);
            }
        }

        return $this;
    }


}
