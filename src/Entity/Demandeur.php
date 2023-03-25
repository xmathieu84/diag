<?php

namespace App\Entity;


use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Controller\API\DemandeurApi;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DemandeurRepository")
 * @ApiResource(normalizationContext={"groups"={"demandeur:read"}},
 *     denormalizationContext={"groups"={"demandeur:write"}},
 *     itemOperations={
 *          "infoDemandeur"={
 *              "method"="GET",
 *              "path"="/infoDemandeur",
 *              "controller"=DemandeurApi::class,
 *               "denormalization_context"={"groups"={"demandeurInfo"}},
 *
 *     }
 *     },
 *     collectionOperations={"post"})
 *
 */

class Demandeur
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups ({"demandeur:read","demandeur:write"})
     *
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     * @Assert\Regex(pattern="/[A-Za-z0-9_'.,-]/",message="Le profil est invalide")
     * @Groups ({"demandeurInfo","demandeur:write"})
     *
     */
    private $profil;



    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Intervention", mappedBy="intDem",cascade={"remove"})
     * @Groups ("demandeur:read")
     */
    private $interventions;




    /**
     * @ORM\Column(type="boolean", nullable=true)
     *
     */
    private $cgv;

    /**
     * @ORM\OneToMany(targetEntity=CGUvente::class, mappedBy="demnadeur")
     *
     *
     */
    private $cGUventes;

    /**
     * @ORM\OneToOne(targetEntity=TvaSiret::class, cascade={"persist", "remove"})
     * @Groups ({"demandeur:read","demandeur:write"})
     *
     *
     */
    private $siretTva;

    /**
     * @ORM\OneToOne(targetEntity=Telephone::class, cascade={"persist", "remove"})
     * @Groups ({"demandeur:read","demandeur:write"})
     *
     *
     */
    private $telephon;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     */
    private $nom;

    /**
     * @ORM\OneToMany(targetEntity=Agent::class, mappedBy="demandeur")
     *
     */
    private $agents;

    /**
     * @ORM\ManyToMany(targetEntity=Rapport::class, mappedBy="consultant")
     */
    private $rapports;



    /**
     * @ORM\OneToOne(targetEntity=Adresse::class, cascade={"persist", "remove"})
     *
     * @Groups ({"demandeur:read","demandeur:write"})
     */
    private $adresse;

    /**
     * @ORM\OneToOne(targetEntity=Civilite::class, cascade={"persist", "remove"})
     * @Groups ({"demandeur:read","demandeur:write"})
     */
    private $civilite;

    /**
     * @ORM\OneToMany(targetEntity=AboTotalInsti::class, mappedBy="demandeur")
     */
    private $aboTotalInstis;

    /**
     * @ORM\OneToMany(targetEntity=FactureInsti::class, mappedBy="institution")
     */
    private $factureInstis;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $logo;

    /**
     * @ORM\OneToMany(targetEntity=Dossier::class, mappedBy="institution")
     */
    private $dossiers;

    /**
     * @ORM\Column(type="string",length=255, nullable=true)
     */
    private $acces;



    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="demandeur", cascade={"persist", "remove"})
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=Banque::class, mappedBy="institution")
     */
    private $banques;

    /**
     * @ORM\ManyToOne(targetEntity=Ambassadeur::class, inversedBy="institution")
     */
    private $ambassadeurInsti;

    /**
     * @ORM\ManyToOne(targetEntity=Ambassadeur::class, inversedBy="grandCompte")
     */
    private $ambassadeurGrandCompte;

    /**
     * @ORM\OneToOne(targetEntity=ProBtp::class, mappedBy="demandeur", cascade={"persist", "remove"})
     */
    private $proBtp;

    /**
     * @ORM\OneToMany(targetEntity=InterDiag::class, mappedBy="demandeur")
     */
    private $interDiags;











    public function __construct()
    {
        $this->interventions = new ArrayCollection();
        $this->cGUventes = new ArrayCollection();
        $this->agents = new ArrayCollection();

        $this->rapports = new ArrayCollection();
        $this->aboTotalInstis = new ArrayCollection();
        $this->factureInstis = new ArrayCollection();
        $this->dossiers = new ArrayCollection();
        $this->sepaSignes = new ArrayCollection();
        $this->banques = new ArrayCollection();
        $this->interDiags = new ArrayCollection();



    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getProfil(): ?string
    {
        return $this->profil;
    }

    public function setProfil(string $profil): self
    {
        $this->profil = $profil;

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
            $intervention->setIntDem($this);
        }

        return $this;
    }

    public function removeIntervention(Intervention $intervention): self
    {
        if ($this->interventions->contains($intervention)) {
            $this->interventions->removeElement($intervention);
            // set the owning side to null (unless already changed)
            if ($intervention->getIntDem() === $this) {
                $intervention->setIntDem(null);
            }
        }

        return $this;
    }

    public function getCgv(): ?bool
    {
        return $this->cgv;
    }

    public function setCgv(?bool $cgv): self
    {
        $this->cgv = $cgv;

        return $this;
    }

    /**
     * @return Collection|CGUvente[]
     */
    public function getCGUventes(): Collection
    {
        return $this->cGUventes;
    }

    public function addCGUvente(CGUvente $cGUvente): self
    {
        if (!$this->cGUventes->contains($cGUvente)) {
            $this->cGUventes[] = $cGUvente;
            $cGUvente->setDemnadeur($this);
        }

        return $this;
    }

    public function removeCGUvente(CGUvente $cGUvente): self
    {
        if ($this->cGUventes->contains($cGUvente)) {
            $this->cGUventes->removeElement($cGUvente);
            // set the owning side to null (unless already changed)
            if ($cGUvente->getDemnadeur() === $this) {
                $cGUvente->setDemnadeur(null);
            }
        }

        return $this;
    }









    public function getSiretTva(): ?TvaSiret
    {
        return $this->siretTva;
    }

    public function setSiretTva(?TvaSiret $siretTva): self
    {
        $this->siretTva = $siretTva;

        return $this;
    }

    public function getTelephon(): ?Telephone
    {
        return $this->telephon;
    }

    public function setTelephon(?Telephone $telephon): self
    {
        $this->telephon = $telephon;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection|Agent[]
     */
    public function getAgents(): Collection
    {
        return $this->agents;
    }

    public function addAgent(Agent $agent): self
    {
        if (!$this->agents->contains($agent)) {
            $this->agents[] = $agent;
            $agent->setDemandeur($this);
        }

        return $this;
    }

    public function removeAgent(Agent $agent): self
    {
        if ($this->agents->removeElement($agent)) {
            // set the owning side to null (unless already changed)
            if ($agent->getDemandeur() === $this) {
                $agent->setDemandeur(null);
            }
        }

        return $this;
    }



    /**
     * @return Collection|Rapport[]
     */
    public function getRapports(): Collection
    {
        return $this->rapports;
    }

    public function addRapport(Rapport $rapport): self
    {
        if (!$this->rapports->contains($rapport)) {
            $this->rapports[] = $rapport;
            $rapport->addConsultant($this);
        }

        return $this;
    }

    public function removeRapport(Rapport $rapport): self
    {
        if ($this->rapports->removeElement($rapport)) {
            $rapport->removeConsultant($this);
        }

        return $this;
    }



    public function getAdresse(): ?Adresse
    {
        return $this->adresse;
    }

    public function setAdresse(?Adresse $adresse): self
    {
        $this->adresse = $adresse;

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

    /**
     * @return Collection|AboTotalInsti[]
     */
    public function getAboTotalInstis(): Collection
    {
        return $this->aboTotalInstis;
    }

    public function addAboTotalInsti(AboTotalInsti $aboTotalInsti): self
    {
        if (!$this->aboTotalInstis->contains($aboTotalInsti)) {
            $this->aboTotalInstis[] = $aboTotalInsti;
            $aboTotalInsti->setDemandeur($this);
        }

        return $this;
    }

    public function removeAboTotalInsti(AboTotalInsti $aboTotalInsti): self
    {
        if ($this->aboTotalInstis->removeElement($aboTotalInsti)) {
            // set the owning side to null (unless already changed)
            if ($aboTotalInsti->getDemandeur() === $this) {
                $aboTotalInsti->setDemandeur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|FactureInsti[]
     */
    public function getFactureInstis(): Collection
    {
        return $this->factureInstis;
    }

    public function addFactureInsti(FactureInsti $factureInsti): self
    {
        if (!$this->factureInstis->contains($factureInsti)) {
            $this->factureInstis[] = $factureInsti;
            $factureInsti->setInstitution($this);
        }

        return $this;
    }

    public function removeFactureInsti(FactureInsti $factureInsti): self
    {
        if ($this->factureInstis->removeElement($factureInsti)) {
            // set the owning side to null (unless already changed)
            if ($factureInsti->getInstitution() === $this) {
                $factureInsti->setInstitution(null);
            }
        }

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * @return Collection|Dossier[]
     */
    public function getDossiers(): Collection
    {
        return $this->dossiers;
    }

    public function addDossier(Dossier $dossier): self
    {
        if (!$this->dossiers->contains($dossier)) {
            $this->dossiers[] = $dossier;
            $dossier->setInstitution($this);
        }

        return $this;
    }

    public function removeDossier(Dossier $dossier): self
    {
        if ($this->dossiers->removeElement($dossier)) {
            // set the owning side to null (unless already changed)
            if ($dossier->getInstitution() === $this) {
                $dossier->setInstitution(null);
            }
        }

        return $this;
    }

    public function getAcces(): ?string
    {
        return $this->acces;
    }

    public function setAcces(?string $acces): self
    {
        $this->acces = $acces;

        return $this;
    }



    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|Banque[]
     */
    public function getBanques(): Collection
    {
        return $this->banques;
    }

    public function addBanque(Banque $banque): self
    {
        if (!$this->banques->contains($banque)) {
            $this->banques[] = $banque;
            $banque->setInstitution($this);
        }

        return $this;
    }

    public function removeBanque(Banque $banque): self
    {
        if ($this->banques->removeElement($banque)) {
            // set the owning side to null (unless already changed)
            if ($banque->getInstitution() === $this) {
                $banque->setInstitution(null);
            }
        }

        return $this;
    }

    public function getAmbassadeurInsti(): ?Ambassadeur
    {
        return $this->ambassadeurInsti;
    }

    public function setAmbassadeurInsti(?Ambassadeur $ambassadeurInsti): self
    {
        $this->ambassadeurInsti = $ambassadeurInsti;

        return $this;
    }

    public function getAmbassadeurGrandCompte(): ?Ambassadeur
    {
        return $this->ambassadeurGrandCompte;
    }

    public function setAmbassadeurGrandCompte(?Ambassadeur $ambassadeurGrandCompte): self
    {
        $this->ambassadeurGrandCompte = $ambassadeurGrandCompte;

        return $this;
    }

    public function getProBtp(): ?ProBtp
    {
        return $this->proBtp;
    }

    public function setProBtp(?ProBtp $proBtp): self
    {
        // unset the owning side of the relation if necessary
        if ($proBtp === null && $this->proBtp !== null) {
            $this->proBtp->setDemandeur(null);
        }

        // set the owning side of the relation if necessary
        if ($proBtp !== null && $proBtp->getDemandeur() !== $this) {
            $proBtp->setDemandeur($this);
        }

        $this->proBtp = $proBtp;

        return $this;
    }

    /**
     * @return Collection<int, InterDiag>
     */
    public function getInterDiags(): Collection
    {
        return $this->interDiags;
    }

    public function addInterDiag(InterDiag $interDiag): self
    {
        if (!$this->interDiags->contains($interDiag)) {
            $this->interDiags[] = $interDiag;
            $interDiag->setDemandeur($this);
        }

        return $this;
    }

    public function removeInterDiag(InterDiag $interDiag): self
    {
        if ($this->interDiags->removeElement($interDiag)) {
            // set the owning side to null (unless already changed)
            if ($interDiag->getDemandeur() === $this) {
                $interDiag->setDemandeur(null);
            }
        }

        return $this;
    }

}
