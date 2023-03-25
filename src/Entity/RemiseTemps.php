<?php

namespace App\Entity;

use App\Repository\RemiseTempsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RemiseTempsRepository::class)
 */
class RemiseTemps
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $journee;

    /**
     * @ORM\Column(type="boolean")
     */
    private $demiJournee;

    /**
     * @ORM\OneToOne(targetEntity=Salarie::class, inversedBy="remiseTemps", cascade={"persist", "remove"})
     */
    private $odi;

    public function getJournee(): ?bool
    {
        return $this->journee;
    }

    public function setJournee(bool $journee): self
    {
        $this->journee = $journee;

        return $this;
    }

    public function getDemiJournee(): ?bool
    {
        return $this->demiJournee;
    }

    public function setDemiJournee(bool $demiJournee): self
    {
        $this->demiJournee = $demiJournee;

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


}
