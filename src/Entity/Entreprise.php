<?php

namespace App\Entity;

use JetBrains\PhpStorm\Pure;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EntrepriseRepository")
 */
class Entreprise extends \phpDocumentor\Reflection\Types\Integer
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(pattern="/[A-Za-z0-9_'.,!-]/",message="Le nom de votre entreprise est invalide")
     */
    private $denomination;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Regex(pattern="/[A-Za-z0-9_'.,!-]/",message="Le nom de votre entreprise est invalide")
     */
    private $enseigne;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(pattern="/[A-Za-z0-9_'.,-]/",message="Le message est invalide")
     */
    private $form_juridique;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\File(mimeTypes={ "image/png", "image/jpeg","image/jpg" }) 
     */
    private $logo;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Adresse", cascade={"persist", "remove"})
     */
    private $adresse;


    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $mailRappel;
    /**
     * @ORM\Column(type="boolean")
     */
    private $cgv;
    /**
     * @ORM\OneToMany(targetEntity=Drone::class, mappedBy="entrepris")
     */
    private $drones;
    /**
     * @ORM\Column(type="float", nullable=true)
     * @Assert\Regex(pattern="/[0-9.]/",message="Le numéro est invalide est invalide")
     */
    private $indeminiteKilometre;



    /**
     * @ORM\OneToMany(targetEntity=EtatAbonnement::class, mappedBy="entreprise")
     */
    private $etatAbonnements;

    /**
     * @ORM\OneToMany(targetEntity=MandatCerfa::class, mappedBy="entreprise")
     */
    private $mandatCerfas;

    /**
     * @ORM\OneToMany(targetEntity=Devis::class, mappedBy="entreprise")
     */
    private $devis;

    /**
     * @ORM\OneToMany(targetEntity=Factures::class, mappedBy="entreprise")
     */
    private $factures;







    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    private $commission;

    /**
     * @ORM\OneToMany(targetEntity=FactureOtd::class, mappedBy="entreprise")
     */
    private $factureOtds;

    /**
     * @ORM\OneToOne(targetEntity=Assurances::class, cascade={"persist", "remove"})
     */
    private $ent_ass;

    /**
     * @ORM\OneToOne(targetEntity=User::class, cascade={"persist", "remove"})
     */
    private $user;

    /**
     * @ORM\OneToOne(targetEntity=Civilite::class, cascade={"persist", "remove"})
     */
    private $dirigeant;



    /**
     * @ORM\OneToOne(targetEntity=TvaSiret::class, cascade={"persist", "remove"})
     */
    private $siretTva;

    /**
     * @ORM\OneToOne(targetEntity=Telephone::class, cascade={"persist", "remove"})
     */
    private $telephone;

    /**
     * @ORM\OneToMany(targetEntity=Salarie::class, mappedBy="entreprise")
     */
    private $salaries;

    /**
     * @ORM\OneToMany(targetEntity=ReponseAo::class, mappedBy="entreprise")
     */
    private $reponseAos;

    /**
     * @ORM\OneToMany(targetEntity=Banque::class, mappedBy="entreprise")
     */
    private $banques;

    /**
     * @ORM\OneToOne(targetEntity=UboDeclaration::class, mappedBy="entreprise", cascade={"persist", "remove"})
     */
    private $uboDeclaration;

    /**
     * @ORM\ManyToOne(targetEntity=Ambassadeur::class, inversedBy="otd")
     */
    private $ambassadeur;

    /**
     * @ORM\OneToMany(targetEntity=KycDeclaration::class, mappedBy="entreprise")
     */
    private $kycDeclarations;










    public function __construct()
    {
        $this->drones = new ArrayCollection();
        $this->etatAbonnements = new ArrayCollection();
        $this->mandatCerfas = new ArrayCollection();
        $this->devis = new ArrayCollection();
        $this->factures = new ArrayCollection();

        $this->factureOtds = new ArrayCollection();
        $this->salaries = new ArrayCollection();
        $this->reponseAos = new ArrayCollection();

        $this->banques = new ArrayCollection();
        $this->kycDeclarations = new ArrayCollection();


    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDenomination(): ?string
    {
        return $this->denomination;
    }

    public function setDenomination(string $denomination): self
    {
        $this->denomination = $denomination;

        return $this;
    }

    public function getEnseigne(): ?string
    {
        return $this->enseigne;
    }

    public function setEnseigne(?string $enseigne): self
    {
        $this->enseigne = $enseigne;

        return $this;
    }

    public function getFormJuridique(): ?string
    {
        return $this->form_juridique;
    }

    public function setFormJuridique(string $form_juridique): self
    {
        $this->form_juridique = $form_juridique;

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

    public function getAdresse(): ?adresse
    {
        return $this->adresse;
    }

    public function setAdresse(?adresse $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }


    public function getMailRappel(): ?\DateTimeInterface
    {
        return $this->mailRappel;
    }

    public function setMailRappel(?\DateTimeInterface $mailRappel): self
    {
        $this->mailRappel = $mailRappel;

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
     * @return Collection|Drone[]
     */
    public function getDrones(): Collection
    {
        return $this->drones;
    }

    public function addDrone(Drone $drone): self
    {
        if (!$this->drones->contains($drone)) {
            $this->drones[] = $drone;
            $drone->setEntrepris($this);
        }

        return $this;
    }

    public function removeDrone(Drone $drone): self
    {
        if ($this->drones->contains($drone)) {
            $this->drones->removeElement($drone);
            // set the owning side to null (unless already changed)
            if ($drone->getEntrepris() === $this) {
                $drone->setEntrepris(null);
            }
        }

        return $this;
    }
    public function getIndeminiteKilometre(): ?float
    {
        return $this->indeminiteKilometre;
    }

    public function setIndeminiteKilometre(?float $indeminiteKilometre): self
    {
        $this->indeminiteKilometre = $indeminiteKilometre;

        return $this;
    }



    /**
     * @return Collection|EtatAbonnement[]
     */
    public function getEtatAbonnements(): Collection
    {
        return $this->etatAbonnements;
    }

    public function addEtatAbonnement(EtatAbonnement $etatAbonnement): self
    {
        if (!$this->etatAbonnements->contains($etatAbonnement)) {
            $this->etatAbonnements[] = $etatAbonnement;
            $etatAbonnement->setEntreprise($this);
        }

        return $this;
    }

    public function removeEtatAbonnement(EtatAbonnement $etatAbonnement): self
    {
        if ($this->etatAbonnements->contains($etatAbonnement)) {
            $this->etatAbonnements->removeElement($etatAbonnement);
            // set the owning side to null (unless already changed)
            if ($etatAbonnement->getEntreprise() === $this) {
                $etatAbonnement->setEntreprise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|MandatCerfa[]
     */
    public function getMandatCerfas(): Collection
    {
        return $this->mandatCerfas;
    }

    public function addMandatCerfa(MandatCerfa $mandatCerfa): self
    {
        if (!$this->mandatCerfas->contains($mandatCerfa)) {
            $this->mandatCerfas[] = $mandatCerfa;
            $mandatCerfa->setEntreprise($this);
        }

        return $this;
    }

    public function removeMandatCerfa(MandatCerfa $mandatCerfa): self
    {
        if ($this->mandatCerfas->contains($mandatCerfa)) {
            $this->mandatCerfas->removeElement($mandatCerfa);
            // set the owning side to null (unless already changed)
            if ($mandatCerfa->getEntreprise() === $this) {
                $mandatCerfa->setEntreprise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Devis[]
     */
    public function getDevis(): Collection
    {
        return $this->devis;
    }

    public function addDevi(Devis $devi): self
    {
        if (!$this->devis->contains($devi)) {
            $this->devis[] = $devi;
            $devi->setEntreprise($this);
        }

        return $this;
    }

    public function removeDevi(Devis $devi): self
    {
        if ($this->devis->contains($devi)) {
            $this->devis->removeElement($devi);
            // set the owning side to null (unless already changed)
            if ($devi->getEntreprise() === $this) {
                $devi->setEntreprise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Factures[]
     */
    public function getFactures(): Collection
    {
        return $this->factures;
    }

    public function addFacture(Factures $facture): self
    {
        if (!$this->factures->contains($facture)) {
            $this->factures[] = $facture;
            $facture->setEntreprise($this);
        }

        return $this;
    }

    public function removeFacture(Factures $facture): self
    {
        if ($this->factures->contains($facture)) {
            $this->factures->removeElement($facture);
            // set the owning side to null (unless already changed)
            if ($facture->getEntreprise() === $this) {
                $facture->setEntreprise(null);
            }
        }

        return $this;
    }



    public function getCommission(): ?int
    {
        return $this->commission;
    }

    public function setCommission(int $commission): self
    {
        $this->commission = $commission;

        return $this;
    }

    /**
     * @return Collection|FactureOtd[]
     */
    public function getFactureOtds(): Collection
    {
        return $this->factureOtds;
    }

    public function addFactureOtd(FactureOtd $factureOtd): self
    {
        if (!$this->factureOtds->contains($factureOtd)) {
            $this->factureOtds[] = $factureOtd;
            $factureOtd->setEntreprise($this);
        }

        return $this;
    }

    public function removeFactureOtd(FactureOtd $factureOtd): self
    {
        if ($this->factureOtds->removeElement($factureOtd)) {
            // set the owning side to null (unless already changed)
            if ($factureOtd->getEntreprise() === $this) {
                $factureOtd->setEntreprise(null);
            }
        }

        return $this;
    }

    public function getEntAss(): ?Assurances
    {
        return $this->ent_ass;
    }

    public function setEntAss(?Assurances $ent_ass): self
    {
        $this->ent_ass = $ent_ass;

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

    public function getDirigeant(): ?Civilite
    {
        return $this->dirigeant;
    }

    public function setDirigeant(?Civilite $dirigeant): self
    {
        $this->dirigeant = $dirigeant;

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

    public function getTelephone(): ?Telephone
    {
        return $this->telephone;
    }

    public function setTelephone(?Telephone $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * @return Collection|Salarie[]
     */
    public function getSalaries(): Collection
    {
        return $this->salaries;
    }

    public function addSalary(Salarie $salary): self
    {
        if (!$this->salaries->contains($salary)) {
            $this->salaries[] = $salary;
            $salary->setEntreprise($this);
        }

        return $this;
    }

    public function removeSalary(Salarie $salary): self
    {
        if ($this->salaries->removeElement($salary)) {
            // set the owning side to null (unless already changed)
            if ($salary->getEntreprise() === $this) {
                $salary->setEntreprise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ReponseAo[]
     */
    public function getReponseAos(): Collection
    {
        return $this->reponseAos;
    }

    public function addReponseAo(ReponseAo $reponseAo): self
    {
        if (!$this->reponseAos->contains($reponseAo)) {
            $this->reponseAos[] = $reponseAo;
            $reponseAo->setEntreprise($this);
        }

        return $this;
    }

    public function removeReponseAo(ReponseAo $reponseAo): self
    {
        if ($this->reponseAos->removeElement($reponseAo)) {
            // set the owning side to null (unless already changed)
            if ($reponseAo->getEntreprise() === $this) {
                $reponseAo->setEntreprise(null);
            }
        }

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
            $banque->setEntreprise($this);
        }

        return $this;
    }

    public function removeBanque(Banque $banque): self
    {
        if ($this->banques->removeElement($banque)) {
            // set the owning side to null (unless already changed)
            if ($banque->getEntreprise() === $this) {
                $banque->setEntreprise(null);
            }
        }

        return $this;
    }

    public function getUboDeclaration(): ?UboDeclaration
    {
        return $this->uboDeclaration;
    }

    public function setUboDeclaration(?UboDeclaration $uboDeclaration): self
    {
        // unset the owning side of the relation if necessary
        if ($uboDeclaration === null && $this->uboDeclaration !== null) {
            $this->uboDeclaration->setEntreprise(null);
        }

        // set the owning side of the relation if necessary
        if ($uboDeclaration !== null && $uboDeclaration->getEntreprise() !== $this) {
            $uboDeclaration->setEntreprise($this);
        }

        $this->uboDeclaration = $uboDeclaration;

        return $this;
    }

    public function getAmbassadeur(): ?Ambassadeur
    {
        return $this->ambassadeur;
    }

    public function setAmbassadeur(?Ambassadeur $ambassadeur): self
    {
        $this->ambassadeur = $ambassadeur;

        return $this;
    }

    /**
     * @return Collection<int, KycDeclaration>
     */
    public function getKycDeclarations(): Collection
    {
        return $this->kycDeclarations;
    }

    public function addKycDeclaration(KycDeclaration $kycDeclaration): self
    {
        if (!$this->kycDeclarations->contains($kycDeclaration)) {
            $this->kycDeclarations[] = $kycDeclaration;
            $kycDeclaration->setEntreprise($this);
        }

        return $this;
    }

    public function removeKycDeclaration(KycDeclaration $kycDeclaration): self
    {
        if ($this->kycDeclarations->removeElement($kycDeclaration)) {
            // set the owning side to null (unless already changed)
            if ($kycDeclaration->getEntreprise() === $this) {
                $kycDeclaration->setEntreprise(null);
            }
        }

        return $this;
    }









}
