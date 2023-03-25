<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DroneRepository::class)
 */
class Drone
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(pattern="/[A-Za-z0-9_'.,!-]/",message="Le nom du fabriquant est invalide")
     */
    private $nomFabriquant;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(pattern="/[A-Za-z0-9_'.,!-]/",message="Le type de dron est invalide est invalide")
     */
    private $typeDrone;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(pattern="/[A-Za-z0-9_'.,!-]/",message="Le numéro est invalide est invalide")
     */
    private $numeroDgac;

    /**
     * @ORM\Column(type="float")
     * @Assert\Regex(pattern="/[0-9_'.,!-]/",message="Le poids indiqué n'est pas correct")
     */
    private $PoidDrone;

    

    /**
     * @ORM\OneToMany(targetEntity=Intervention::class, mappedBy="drone")
     */
    private $interventions;

    /**
     * @ORM\ManyToOne(targetEntity=Entreprise::class, inversedBy="drones")
     */
    private $entrepris;

    /**
     * @ORM\Column(type="boolean")
     */
    private $captif;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(pattern="/[A-Za-z0-9_'.,!-]/",message="La classe n'est pas valide")
     */
    private $classe;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Regex(pattern="/[A-Za-z0-9_'.,!-]/",message="Caractères non autorisé")
     */
    private $trame;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $marqueCEE;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $serial;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $actif;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $vitesse;

    public function __construct()
    {
        $this->interventions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomFabriquant(): ?string
    {
        return $this->nomFabriquant;
    }

    public function setNomFabriquant(string $nomFabriquant): self
    {
        $this->nomFabriquant = $nomFabriquant;

        return $this;
    }
    public function getTypeDrone(): ?string
    {
        return $this->typeDrone;
    }

    public function setTypeDrone(string $typeDrone): self
    {
        $this->typeDrone = $typeDrone;

        return $this;
    }

    

    public function getnumeroDgac(): ?string
    {
        return $this->numeroDgac;
    }

    public function setnumeroDgac(string $numeroDgac): self
    {
        $this->numeroDgac = $numeroDgac;

        return $this;
    }

    public function getPoidDrone(): ?float
    {
        return $this->PoidDrone;
    }

    public function setPoidDrone(float $PoidDrone): self
    {
        $this->PoidDrone = $PoidDrone;

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

    /**
     * @return Collection|Intervention[]
     */
    public function getInterventions(): Collection
    {
        return $this->interventions;
    }

    public function addIntervention(Intervention $intervention): self
    {
        if (!$this->interventions->contains($intervention)) {
            $this->interventions[] = $intervention;
            $intervention->setDrone($this);
        }

        return $this;
    }

    public function removeIntervention(Intervention $intervention): self
    {
        if ($this->interventions->contains($intervention)) {
            $this->interventions->removeElement($intervention);
            // set the owning side to null (unless already changed)
            if ($intervention->getDrone() === $this) {
                $intervention->setDrone(null);
            }
        }

        return $this;
    }

    public function getEntrepris(): ?Entreprise
    {
        return $this->entrepris;
    }

    public function setEntrepris(?Entreprise $entrepris): self
    {
        $this->entrepris = $entrepris;

        return $this;
    }

    public function getCaptif(): ?bool
    {
        return $this->captif;
    }

    public function setCaptif(bool $captif): self
    {
        $this->captif = $captif;

        return $this;
    }

    public function getClasse(): ?string
    {
        return $this->classe;
    }

    public function setClasse(string $classe): self
    {
        $this->classe = $classe;

        return $this;
    }

    public function getTrame(): ?string
    {
        return $this->trame;
    }

    public function setTrame(?string $trame): self
    {
        $this->trame = $trame;

        return $this;
    }

    public function getMarqueCEE(): ?string
    {
        return $this->marqueCEE;
    }

    public function setMarqueCEE(?string $marqueCEE): self
    {
        $this->marqueCEE = $marqueCEE;

        return $this;
    }

    public function getSerial(): ?string
    {
        return $this->serial;
    }

    public function setSerial(?string $serial): self
    {
        $this->serial = $serial;

        return $this;
    }

    public function getActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(?bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }

    public function getVitesse(): ?float
    {
        return $this->vitesse;
    }

    public function setVitesse(?float $vitesse): self
    {
        $this->vitesse = $vitesse;

        return $this;
    }
}
