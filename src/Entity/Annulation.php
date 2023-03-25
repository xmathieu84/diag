<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AnnulationRepository")
 */
class Annulation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Regex(pattern="/[A-Za-z0-9_'.,]/",message="Le message est invalide")
     */
    private $raison;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Salarie", inversedBy="annulations")
     */
    private $salarie;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Intervention", inversedBy="annulations")
     */
    private $intervention;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRaison(): ?string
    {
        return $this->raison;
    }

    public function setRaison(?string $raison): self
    {
        $this->raison = $raison;

        return $this;
    }

    public function getSalarie(): ?salarie
    {
        return $this->salarie;
    }

    public function setSalarie(?salarie $salarie): self
    {
        $this->salarie = $salarie;

        return $this;
    }

    public function getIntervention(): ?intervention
    {
        return $this->intervention;
    }

    public function setIntervention(?intervention $intervention): self
    {
        $this->intervention = $intervention;

        return $this;
    }
}
