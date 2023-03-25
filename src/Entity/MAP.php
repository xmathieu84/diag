<?php

namespace App\Entity;

use App\Repository\MAPRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MAPRepository::class)
 */
class MAP
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="dateinterval", nullable=true)
     */
    private $dureeVol;



    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Regex(pattern="/[A-Za-z0-9_'.,]/",message="Abservation invalide")
     */
    private $observation;

    /**
     * @ORM\OneToOne(targetEntity=Intervention::class, mappedBy="mAP", cascade={"persist", "remove"})
     */
    private $intervention;





    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDureeVol(): ?\DateInterval
    {
        return $this->dureeVol;
    }

    public function setDureeVol(?\DateInterval $dureeVol): self
    {
        $this->dureeVol = $dureeVol;

        return $this;
    }



    public function getObservation(): ?string
    {
        return $this->observation;
    }

    public function setObservation(?string $observation): self
    {
        $this->observation = $observation;

        return $this;
    }

    public function getIntervention(): ?Intervention
    {
        return $this->intervention;
    }

    public function setIntervention(?Intervention $intervention): self
    {
        // unset the owning side of the relation if necessary
        if ($intervention === null && $this->intervention !== null) {
            $this->intervention->setMAP(null);
        }

        // set the owning side of the relation if necessary
        if ($intervention !== null && $intervention->getMAP() !== $this) {
            $intervention->setMAP($this);
        }

        $this->intervention = $intervention;

        return $this;
    }


}
