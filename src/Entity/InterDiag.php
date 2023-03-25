<?php

namespace App\Entity;

use App\Repository\InterDiagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * @ORM\Entity(repositoryClass=InterDiagRepository::class)
 */
class InterDiag
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Demandeur::class, inversedBy="interDiags")
     */
    private $demandeur;

    /**
     * @ORM\OneToOne(targetEntity=Adresse::class, inversedBy="interDiag", cascade={"persist", "remove"})
     */
    private $adresse;

    /**
     * @ORM\Column(type="date")
     */
    private $dateDemande;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateRdv;

    /**
     * @ORM\ManyToOne(targetEntity=TailleBien::class, inversedBy="interDiags")
     */
    private $tailleBien;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $typeDiag;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $prix;

    /**
     * @ORM\Column(type="float", nullable=true)
     */



    private $acompte;

    /**
     * @ORM\Column(type="string",length=255, nullable=true)
     */
    private $ageGaz;

    /**
     * @ORM\Column(type="string",length=255, nullable=true)
     */
    private $ageElec;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $amiante;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $plomb;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $gaz;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $electricite;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $permis;

    /**
     * @ORM\ManyToMany(targetEntity=Mission::class, inversedBy="interDiags")
     */
    private $missions;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $heureRdv;

    /**
     * @ORM\Column(type="dateinterval", nullable=true)
     */
    private $dureeRdv;

    /**
     * @ORM\ManyToOne(targetEntity=Salarie::class, inversedBy="interDiags")
     */
    private $odi;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $statut;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $moment;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $factureAcompte;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $facture;

    /**
     * @ORM\Column(type="uuid",unique=true)
     */
    private $identifiat;

    /**
     * @ORM\ManyToOne(targetEntity=PackOdiPrixTaille::class, inversedBy="interDiags")
     */
    private $pack;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $remiseTemps;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $datePaiement;

    /**
     * @ORM\OneToOne(targetEntity=MangoPayIn::class, inversedBy="interDiag", cascade={"persist", "remove"})
     */
    private $mangoPayIn;

    public function __construct()
    {
        $this->dateDemande = new \DateTime();
        $this->missions = new ArrayCollection();
        $this->identifiat = Uuid::v4();
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

    public function getAdresse(): ?Adresse
    {
        return $this->adresse;
    }

    public function setAdresse(?Adresse $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getDateDemande(): ?\DateTimeInterface
    {
        return $this->dateDemande;
    }

    public function setDateDemande(\DateTimeInterface $dateDemande): self
    {
        $this->dateDemande = $dateDemande;

        return $this;
    }

    public function getDateRdv(): ?\DateTimeInterface
    {
        return $this->dateRdv;
    }

    public function setDateRdv(?\DateTimeInterface $dateRdv): self
    {
        $this->dateRdv = $dateRdv;

        return $this;
    }

    public function getTailleBien(): ?TailleBien
    {
        return $this->tailleBien;
    }

    public function setTailleBien(?TailleBien $tailleBien): self
    {
        $this->tailleBien = $tailleBien;

        return $this;
    }

    public function getTypeDiag(): ?string
    {
        return $this->typeDiag;
    }

    public function setTypeDiag(?string $typeDiag): self
    {
        $this->typeDiag = $typeDiag;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(?float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getAcompte(): ?float
    {
        return $this->acompte;
    }

    public function setAcompte(?float $acompte): self
    {
        $this->acompte = $acompte;

        return $this;
    }

    public function getAgeGaz(): ?string
    {
        return $this->ageGaz;
    }

    public function setAgeGaz(?string $ageGaz): self
    {
        $this->ageGaz = $ageGaz;

        return $this;
    }

    public function getAgeElec(): ?string
    {
        return $this->ageElec;
    }

    public function setAgeElec(?string $ageElec): self
    {
        $this->ageElec = $ageElec;

        return $this;
    }

    public function getAmiante(): ?bool
    {
        return $this->amiante;
    }

    public function setAmiante(?bool $amiante): self
    {
        $this->amiante = $amiante;

        return $this;
    }

    public function getPlomb(): ?bool
    {
        return $this->plomb;
    }

    public function setPlomb(?bool $plomb): self
    {
        $this->plomb = $plomb;

        return $this;
    }

    public function getGaz(): ?bool
    {
        return $this->gaz;
    }

    public function setGaz(?bool $gaz): self
    {
        $this->gaz = $gaz;

        return $this;
    }

    public function getElectricite(): ?bool
    {
        return $this->electricite;
    }

    public function setElectricite(?bool $electricite): self
    {
        $this->electricite = $electricite;

        return $this;
    }

    public function getPermis(): ?string
    {
        return $this->permis;
    }

    public function setPermis(?string $permis): self
    {
        $this->permis = $permis;

        return $this;
    }

    /**
     * @return Collection<int, Mission>
     */
    public function getMissions(): Collection
    {
        return $this->missions;
    }

    public function addMission(Mission $mission): self
    {
        if (!$this->missions->contains($mission)) {
            $this->missions[] = $mission;
        }

        return $this;
    }

    public function removeMission(Mission $mission): self
    {
        $this->missions->removeElement($mission);

        return $this;
    }

    public function getHeureRdv(): ?\DateTimeInterface
    {
        return $this->heureRdv;
    }

    public function setHeureRdv(?\DateTimeInterface $heureRdv): self
    {
        $this->heureRdv = $heureRdv;

        return $this;
    }

    public function getDureeRdv(): ?\DateInterval
    {
        return $this->dureeRdv;
    }

    public function setDureeRdv(?\DateInterval $dureeRdv): self
    {
        $this->dureeRdv = $dureeRdv;

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

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getMoment(): ?string
    {
        return $this->moment;
    }

    public function setMoment(?string $moment): self
    {
        $this->moment = $moment;

        return $this;
    }

    public function getFactureAcompte(): ?string
    {
        return $this->factureAcompte;
    }

    public function setFactureAcompte(?string $factureAcompte): self
    {
        $this->factureAcompte = $factureAcompte;

        return $this;
    }

    public function getFacture(): ?string
    {
        return $this->facture;
    }

    public function setFacture(?string $facture): self
    {
        $this->facture = $facture;

        return $this;
    }

    public function getIdentifiat()
    {
        return $this->identifiat;
    }

    public function setIdentifiat($identifiat): self
    {
        $this->identifiat = $identifiat;

        return $this;
    }

    public function getPack(): ?PackOdiPrixTaille
    {
        return $this->pack;
    }

    public function setPack(?PackOdiPrixTaille $pack): self
    {
        $this->pack = $pack;

        return $this;
    }

    public function getRemiseTemps(): ?float
    {
        return $this->remiseTemps;
    }

    public function setRemiseTemps(?float $remiseTemps): self
    {
        $this->remiseTemps = $remiseTemps;

        return $this;
    }

    public function getDatePaiement(): ?\DateTimeInterface
    {
        return $this->datePaiement;
    }

    public function setDatePaiement(?\DateTimeInterface $datePaiement): self
    {
        $this->datePaiement = $datePaiement;

        return $this;
    }

    public function getMangoPayIn(): ?MangoPayIn
    {
        return $this->mangoPayIn;
    }

    public function setMangoPayIn(?MangoPayIn $mangoPayIn): self
    {
        $this->mangoPayIn = $mangoPayIn;

        return $this;
    }




}
