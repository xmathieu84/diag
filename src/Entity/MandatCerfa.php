<?php

namespace App\Entity;

use App\Repository\MandatCerfaRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=MandatCerfaRepository::class)
 */
class MandatCerfa
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\File(mimeTypes={ "application/pdf" })
     */
    private $fichierMandat;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\File(mimeTypes={ "application/pdf" })
     */
    private $cerfa;

    /**
     * @ORM\ManyToOne(targetEntity=Entreprise::class, inversedBy="mandatCerfas")
     */
    private $entreprise;

    /**
     * @ORM\OneToOne(targetEntity=Intervention::class, mappedBy="mandatCerfa", cascade={"persist", "remove"})
     */
    private $intervention;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mandatSigne;




    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFichierMandat(): ?string
    {
        return $this->fichierMandat;
    }

    public function setFichierMandat(string $fichierMandat): self
    {
        $this->fichierMandat = $fichierMandat;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getCerfa(): ?string
    {
        return $this->cerfa;
    }

    public function setCerfa(?string $cerfa): self
    {
        $this->cerfa = $cerfa;

        return $this;
    }

    public function getEntreprise(): ?Entreprise
    {
        return $this->entreprise;
    }

    public function setEntreprise(?Entreprise $entreprise): self
    {
        $this->entreprise = $entreprise;

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
            $this->intervention->setMandatCerfa(null);
        }

        // set the owning side of the relation if necessary
        if ($intervention !== null && $intervention->getMandatCerfa() !== $this) {
            $intervention->setMandatCerfa($this);
        }

        $this->intervention = $intervention;

        return $this;
    }

    public function getMandatSigne(): ?string
    {
        return $this->mandatSigne;
    }

    public function setMandatSigne(?string $mandatSigne): self
    {
        $this->mandatSigne = $mandatSigne;

        return $this;
    }


}
