<?php

namespace App\Entity;

use App\Repository\AgentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AgentRepository::class)
 */
class Agent
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="agent", cascade={"persist", "remove"})
     */
    private $user;



    /**
     * @ORM\ManyToOne(targetEntity=Demandeur::class, inversedBy="agents")
     */
    private $demandeur;



    /**
     * @ORM\OneToOne(targetEntity=Planning::class, cascade={"persist", "remove"})
     */
    private $planning;

    /**
     * @ORM\OneToOne(targetEntity=Civilite::class, cascade={"persist", "remove"})
     */
    private $civilite;




    /**
     * @ORM\ManyToOne(targetEntity=Agent::class, inversedBy="chef")
     */
    private $employe;

    /**
     * @ORM\OneToMany(targetEntity=Agent::class, mappedBy="employe")
     */
    private $chef;



    /**
     * @ORM\OneToMany(targetEntity=AppelOffre::class, mappedBy="agents")
     */
    private $appelOffres;

    /**
     * @ORM\Column(type="boolean")
     */
    private $cgv;

    /**
     * @ORM\ManyToMany(targetEntity=Agent::class, inversedBy="superieur")
     */
    private $responsable;

    /**
     * @ORM\ManyToMany(targetEntity=Agent::class, mappedBy="responsable")
     */
    private $superieur;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $identifiant;

    /**
     * @ORM\OneToMany(targetEntity=DemandeAcces::class, mappedBy="syndic")
     */
    private $demandeAcces;

    /**
     * @ORM\OneToMany(targetEntity=DemandeAcces::class, mappedBy="syndicat")
     */
    private $demandeAccesSyndicat;


    public function __construct()
    {


        $this->chef = new ArrayCollection();

        $this->appelOffres = new ArrayCollection();
        $this->responsable = new ArrayCollection();
        $this->superieur = new ArrayCollection();
        $this->demandeAcces = new ArrayCollection();
        $this->demandeAccesSyndicat = new ArrayCollection();

    }



    public function getId(): ?int
    {
        return $this->id;
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

    public function getUser():?User
    {
        return $this->user;
    }


    public function setUser($user):self
    {
        $this->user = $user;
        return $this;
    }



    public function getPlanning(): ?Planning
    {
        return $this->planning;
    }

    public function setPlanning(?Planning $planning): self
    {
        $this->planning = $planning;

        return $this;
    }

    public function getCivilite(): ?Civilite
    {
        return $this->civilite;
    }

    public function setCivilite(?Civilite $civilite): self
    {
        $this->civilite = $civilite;

        return $this;
    }



    public function getEmploye(): ?self
    {
        return $this->employe;
    }

    public function setEmploye(?self $employe): self
    {
        $this->employe = $employe;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getChef(): Collection
    {
        return $this->chef;
    }

    public function addChef(self $chef): self
    {
        if (!$this->chef->contains($chef)) {
            $this->chef[] = $chef;
            $chef->setEmploye($this);
        }

        return $this;
    }

    public function removeChef(self $chef): self
    {
        if ($this->chef->removeElement($chef)) {
            // set the owning side to null (unless already changed)
            if ($chef->getEmploye() === $this) {
                $chef->setEmploye(null);
            }
        }

        return $this;
    }



    /**
     * @return Collection|AppelOffre[]
     */
    public function getAppelOffres(): Collection
    {
        return $this->appelOffres;
    }

    public function addAppelOffre(AppelOffre $appelOffre): self
    {
        if (!$this->appelOffres->contains($appelOffre)) {
            $this->appelOffres[] = $appelOffre;
            $appelOffre->setAgents($this);
        }

        return $this;
    }

    public function removeAppelOffre(AppelOffre $appelOffre): self
    {
        if ($this->appelOffres->removeElement($appelOffre)) {
            // set the owning side to null (unless already changed)
            if ($appelOffre->getAgents() === $this) {
                $appelOffre->setAgents(null);
            }
        }

        return $this;
    }

    public function getCgv(): ?bool
    {
        return $this->cgv;
    }

    public function setCgv(bool $cgv): self
    {
        $this->cgv = $cgv;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getResponsable(): Collection
    {
        return $this->responsable;
    }

    public function addResponsable(self $responsable): self
    {
        if (!$this->responsable->contains($responsable)) {
            $this->responsable[] = $responsable;
        }

        return $this;
    }

    public function removeResponsable(self $responsable): self
    {
        $this->responsable->removeElement($responsable);

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getSuperieur(): Collection
    {
        return $this->superieur;
    }

    public function addSuperieur(self $superieur): self
    {
        if (!$this->superieur->contains($superieur)) {
            $this->superieur[] = $superieur;
            $superieur->addResponsable($this);
        }

        return $this;
    }

    public function removeSuperieur(self $superieur): self
    {
        if ($this->superieur->removeElement($superieur)) {
            $superieur->removeResponsable($this);
        }

        return $this;
    }

    public function getIdentifiant(): ?string
    {
        return $this->identifiant;
    }

    public function setIdentifiant(?string $identifiant): self
    {
        $this->identifiant = $identifiant;

        return $this;
    }

    /**
     * @return Collection|DemandeAcces[]
     */
    public function getDemandeAcces(): Collection
    {
        return $this->demandeAcces;
    }

    public function addDemandeAcce(DemandeAcces $demandeAcce): self
    {
        if (!$this->demandeAcces->contains($demandeAcce)) {
            $this->demandeAcces[] = $demandeAcce;
            $demandeAcce->setSyndic($this);
        }

        return $this;
    }

    public function removeDemandeAcce(DemandeAcces $demandeAcce): self
    {
        if ($this->demandeAcces->removeElement($demandeAcce)) {
            // set the owning side to null (unless already changed)
            if ($demandeAcce->getSyndic() === $this) {
                $demandeAcce->setSyndic(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|DemandeAcces[]
     */
    public function getDemandeAccesSyndicat(): Collection
    {
        return $this->demandeAccesSyndicat;
    }

    public function addDemandeAccesSyndicat(DemandeAcces $demandeAccesSyndicat): self
    {
        if (!$this->demandeAccesSyndicat->contains($demandeAccesSyndicat)) {
            $this->demandeAccesSyndicat[] = $demandeAccesSyndicat;
            $demandeAccesSyndicat->setSyndicat($this);
        }

        return $this;
    }

    public function removeDemandeAccesSyndicat(DemandeAcces $demandeAccesSyndicat): self
    {
        if ($this->demandeAccesSyndicat->removeElement($demandeAccesSyndicat)) {
            // set the owning side to null (unless already changed)
            if ($demandeAccesSyndicat->getSyndicat() === $this) {
                $demandeAccesSyndicat->setSyndicat(null);
            }
        }

        return $this;
    }




}
