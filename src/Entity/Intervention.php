<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\API\InterApi;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use App\Controller\API\Etape2Api;
use App\Controller\API\Etape3Api;
use App\Controller\API\Etape4Api;
use App\Controller\API\ChoixProposition;



/**
 * @ORM\Entity(repositoryClass="App\Repository\InterventionRepository")
 * @ApiResource(
 *      normalizationContext={"groups"={"inter:read"}},
 *
 *     collectionOperations={
 *     "etape1"={
 *         "method"="POST",
 *         "path"="/etape1",
 *         "controller"=InterApi::class,
 *           "denormalization_context"={"groups"={"etape1"}}
 * }
 *     },
 *     itemOperations={
 *     "get",
*       "etape2"={
 *            "method"="PATCH",
 *            "path"="/etape2/{id}",
 *            "controller"=Etape2Api::class,
 *      "denormalization_context"={"groups"={"etape2"}}
 *     },
 *     "etape3"={
 *          "method"="PATCH",
 *          "path"="/etape3/{id}",
 *          "controller"=Etape3Api::class,
 *          "denormalization_context"={"groups"={"etape3"}}
 *
 *     },
 *      "etape4"={
 *          "method"="PATCH",
 *          "path"="/etape4/{id}",
 *          "controller"=Etape4Api::class,
 *          "denormalization_context"={"groups"={"etape4"}}
 *
 *     },
 *     "choixProp"={
 *              "method"="PATCH",
 *              "path"="/choixProp/{id}",
 *              "controller"=ChoixProposition::class,
 *              "denormalization_context"={"groups"={"choixProp"}}
 *     }
 *
 *     })
 */

class Intervention implements JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     *
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     * @Assert\Regex(pattern="/[oui non]/",message="Reponse invalide")
     */
    private $urgent;


    /**
     * @ORM\Column(type="string", length=3,nullable =true)
     * @Assert\Regex(pattern="/[oui non]/",message="Reponse invalide")
     * @Groups ({"etape2","inter:read","demandeur:read"})
     */
    private $agglo;




    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Regex(pattern="/[A-Za-z0-9_'.,]/",message="Le message est invalide")
     * @Groups ({"etape4","inter:read","demandeur:read"})
     */
    private $interPrecision;
    /**
     * @ORM\Column(type="string", length=3, nullable=true)
     * @Assert\Regex(pattern="/[oui non]/",message="Reponse invalide")
     * @Groups ({"etape2","inter:read","demandeur:read"})
     */
    private $decollage;

    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     */
    private $statuInter;



    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Demandeur", inversedBy="interventions")
     */
    private $intDem;

    /**
     * @ORM\Column(type="datetime",nullable=true)
     */
    private $createdAT;

    /**
     * @ORM\Column(type="datetime",nullable=true)
     * @Groups ({"inter:read","demandeur:read"})
     */
    private $rdvAT;

    /**
     * @ORM\Column(type="string",length=255,nullable=true)
     */
    private $devis;

    /**
     * @ORM\Column(type="string",length=255,nullable=true)
     */
    private $facture;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Adresse", cascade={"persist", "remove"})
     * @Groups ({"etape3","inter:read","demandeur:read"})
     */
    private $adresse;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $acommpte;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $prix;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Annulation", mappedBy="intervention")
     */
    private $annulations;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Note", cascade={"persist", "remove"})
     */
    private $note;



    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Photo", mappedBy="intervention",cascade = {"persist"})
     * @Groups ({"etape4"})
     */
    private $photoInter;



    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $rappelSMS;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateAccompte;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $datePaiement;

    /**
     * @ORM\ManyToOne(targetEntity=Drone::class, inversedBy="interventions",fetch="EAGER")
     *
     */
    private $drone;



    /**
     * @ORM\ManyToOne(targetEntity=TypInter::class, inversedBy="interventions")
     * @Groups ({"etape1","demandeur:read"})
     */
    private $typeInter;

    /**
     * @ORM\ManyToOne(targetEntity=ListeInter::class, inversedBy="interventions")
     * @Groups ({"etape1","demandeur:read"})
     */
    private $listeInter;

    /**
     * @ORM\OneToMany(targetEntity=MangoPayIn::class, mappedBy="intervention")
     */
    private $mangoPayIns;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups ({"etape2","demandeur:read"})
     */
    private $typeDeBien;

    /**
     * @ORM\Column(type="string",length=255,nullable=true)
     * @Groups ({"etape4","demandeur:read"})
     */
    private $typeDemande ;



    /**
     * @ORM\OneToOne(targetEntity=CGUvente::class, cascade={"persist", "remove"})
     */
    private $cGUvente;

    /**
     * @ORM\OneToOne(targetEntity=Incident::class, cascade={"persist", "remove"})
     */
    private $incident;



    /**
     *
     * @Groups ("etape2")
     */
    private $dateInter;

    /**
     * @Groups ("choixProp")
     */
    public ?int $prop=null;




    /**
     * @ORM\OneToMany(targetEntity=Proposition::class, mappedBy="inter")
     * @Groups ({"inter:read"})
     * @MaxDepth (9)
     */
    private $propositions;

    /**
     * @ORM\OneToOne(targetEntity=Proposition::class, cascade={"persist", "remove"})
     * @Groups ("choisProp")
     */
    private $propositionChoisie;



    /**
     * @ORM\OneToOne(targetEntity=Reservation::class, mappedBy="intervention", cascade={"persist", "remove"})
     */
    private $reservation;



    /**
     * @ORM\OneToOne(targetEntity=MandatCerfa::class, inversedBy="intervention", cascade={"persist", "remove"})
     */
    private $mandatCerfa;

    /**
     * @ORM\Column(type="json", nullable=true)
     * @Groups ("etape4")
     */
    private $intemperie = [];

    /**
     * @ORM\Column(type="boolean",nullable=true)
     */
    private $RenoncementDelaiRetract;

    /**
     * @ORM\OneToOne(targetEntity=MAP::class, inversedBy="intervention", cascade={"persist", "remove"})
     */
    private $mAP;

    /**
     * @ORM\OneToMany(targetEntity=Rapport::class, mappedBy="intervention")
     */
    private $rapports;

    /**
     * @ORM\OneToOne(targetEntity=Factures::class, mappedBy="intervention", cascade={"persist", "remove"})
     */
    private $factures;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @ORM\OneToOne(targetEntity=MeteoInter::class, mappedBy="intervention", cascade={"persist", "remove"})
     */
    private $meteoInter;

    /**
     * @ORM\ManyToMany(targetEntity=Travaux::class, mappedBy="intervention")
     */
    private $travauxes;

    /**
     * @ORM\OneToOne(targetEntity=BudgetInter::class, mappedBy="intervention", cascade={"persist", "remove"})
     */
    private $budgetInter;

    /**
     * @ORM\OneToMany(targetEntity=ContrainteInter::class, mappedBy="intervention")
     */
    private $contrainteInters;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nbrePhoto;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nbreVideo;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateWitch;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateDebut;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateFin;










    public function __construct()
    {

        $this->photoInter = new ArrayCollection();
        $this->mangoPayIns = new ArrayCollection();
        $this->propositions = new ArrayCollection();
        $this->rapports = new ArrayCollection();
        $this->travauxes = new ArrayCollection();
        $this->contrainteInters = new ArrayCollection();

    }




    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrgent(): ?string
    {
        return $this->urgent;
    }

    public function setUrgent(string $urgent): self
    {
        $this->urgent = $urgent;

        return $this;
    }
    public function getAgglo(): ?string
    {
        return $this->agglo;
    }

    public function setAgglo(string $agglo): self
    {
        $this->agglo = $agglo;

        return $this;
    }



    public function getInterPrecision(): ?string
    {
        return $this->interPrecision;
    }

    public function setInterPrecision(?string $interPrecision): self
    {
        $this->interPrecision = $interPrecision;

        return $this;
    }







    public function getDecollage(): ?string
    {
        return $this->decollage;
    }

    public function setDecollage(string $decollage): self
    {
        $this->decollage = $decollage;

        return $this;
    }

    public function getStatuInter(): ?string
    {
        return $this->statuInter;
    }

    public function setStatuInter(string $statuInter): self
    {
        $this->statuInter = $statuInter;

        return $this;
    }





    public function getIntDem(): ?Demandeur
    {
        return $this->intDem;
    }

    public function setIntDem(?Demandeur $intDem): self
    {
        $this->intDem = $intDem;

        return $this;
    }





    public function getCreatedAT(): ?\DateTimeInterface
    {
        return $this->createdAT;
    }

    public function setCreatedAT(\DateTimeInterface $createdAT): self
    {
        $this->createdAT = $createdAT;

        return $this;
    }

    public function getRdvAT(): ?\DateTimeInterface
    {
        return $this->rdvAT;
    }

    public function setRdvAT(\DateTimeInterface $rdvAT): self
    {
        $this->rdvAT = $rdvAT;

        return $this;
    }

    public function getDevis(): ?string
    {
        return $this->devis;
    }

    public function setDevis(?string $devis): self
    {
        $this->devis = $devis;

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

    public function getAdresse(): ?Adresse
    {
        return $this->adresse;
    }

    public function setAdresse(?Adresse $adresse): self
    {
        $this->adresse = $adresse;

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
    public function getAcommpte(): ?float
    {
        return $this->acommpte;
    }

    public function setAcommpte(?float $acommpte): self
    {
        $this->acommpte = $acommpte;

        return $this;
    }

    /**
     * @return Collection|Annulation[]
     */
    public function getAnnulations(): Collection
    {
        return $this->annulations;
    }

    public function addAnnulation(Annulation $annulation): self
    {
        if (!$this->annulations->contains($annulation)) {
            $this->annulations[] = $annulation;
            $annulation->setIntervention($this);
        }

        return $this;
    }

    public function removeAnnulation(Annulation $annulation): self
    {
        if ($this->annulations->contains($annulation)) {
            $this->annulations->removeElement($annulation);
            // set the owning side to null (unless already changed)
            if ($annulation->getIntervention() === $this) {
                $annulation->setIntervention(null);
            }
        }

        return $this;
    }

    public function getNote(): ?note
    {
        return $this->note;
    }

    public function setNote(?note $note): self
    {
        $this->note = $note;

        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize():array
    {
        return $this->toArray();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return get_object_vars($this);
    }

    /**
     * @return Collection|Photo[]
     */
    public function getPhotoInter(): Collection
    {
        return $this->photoInter;
    }

    public function addPhotoInter(Photo $photoInter): self
    {
        if (!$this->photoInter->contains($photoInter)) {
            $this->photoInter[] = $photoInter;
            $photoInter->setIntervention($this);
        }

        return $this;
    }

    public function removePhotoInter(Photo $photoInter): self
    {
        if ($this->photoInter->contains($photoInter)) {
            $this->photoInter->removeElement($photoInter);
            // set the owning side to null (unless already changed)
            if ($photoInter->getIntervention() === $this) {
                $photoInter->setIntervention(null);
            }
        }

        return $this;
    }



    public function getRappelSMS(): ?string
    {
        return $this->rappelSMS;
    }

    public function setRappelSMS(?string $rappelSMS): self
    {
        $this->rappelSMS = $rappelSMS;

        return $this;
    }

    public function getDateAccompte(): ?\DateTimeInterface
    {
        return $this->dateAccompte;
    }

    public function setDateAccompte(?\DateTimeInterface $dateAccompte): self
    {
        $this->dateAccompte = $dateAccompte;

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

    public function getDrone(): ?Drone
    {
        return $this->drone;
    }

    public function setDrone(?Drone $drone): self
    {
        $this->drone = $drone;

        return $this;
    }
    public function getTypeInter(): ?TypInter
    {
        return $this->typeInter;
    }

    public function setTypeInter(?TypInter $typeInter): self
    {
        $this->typeInter = $typeInter;

        return $this;
    }

    public function getListeInter(): ?ListeInter
    {
        return $this->listeInter;
    }

    public function setListeInter(?ListeInter $listeInter): self
    {
        $this->listeInter = $listeInter;

        return $this;
    }



    /**
     * @return Collection|MangoPayIn[]
     */
    public function getMangoPayIns(): Collection
    {
        return $this->mangoPayIns;
    }

    public function addMangoPayIn(MangoPayIn $mangoPayIn): self
    {
        if (!$this->mangoPayIns->contains($mangoPayIn)) {
            $this->mangoPayIns[] = $mangoPayIn;
            $mangoPayIn->setIntervention($this);
        }

        return $this;
    }

    public function removeMangoPayIn(MangoPayIn $mangoPayIn): self
    {
        if ($this->mangoPayIns->removeElement($mangoPayIn)) {
            // set the owning side to null (unless already changed)
            if ($mangoPayIn->getIntervention() === $this) {
                $mangoPayIn->setIntervention(null);
            }
        }

        return $this;
    }

    public function getTypeDeBien(): ?string
    {
        return $this->typeDeBien;
    }

    public function setTypeDeBien(?string $typeDeBien): self
    {
        $this->typeDeBien = $typeDeBien;

        return $this;
    }

    public function getTypeDemande(): ?string
    {
        return $this->typeDemande;
    }

    public function setTypeDemande(?string $typeDemande): self
    {
        $this->typeDemande = $typeDemande;

        return $this;
    }



    public function getCGUvente(): ?CGUvente
    {
        return $this->cGUvente;
    }

    public function setCGUvente(?CGUvente $cGUvente): self
    {
        $this->cGUvente = $cGUvente;

        return $this;
    }

    public function getIncident(): ?Incident
    {
        return $this->incident;
    }

    public function setIncident(?Incident $incident): self
    {
        $this->incident = $incident;

        return $this;
    }





    

    /**
     * @return Collection|Proposition[]
     */
    public function getPropositions(): Collection
    {
        return $this->propositions;
    }

    public function addProposition(Proposition $proposition): self
    {
        if (!$this->propositions->contains($proposition)) {
            $this->propositions[] = $proposition;
            $proposition->setInter($this);
        }

        return $this;
    }

    public function removeProposition(Proposition $proposition): self
    {
        if ($this->propositions->removeElement($proposition)) {
            // set the owning side to null (unless already changed)
            if ($proposition->getInter() === $this) {
                $proposition->setInter(null);
            }
        }

        return $this;
    }

    public function getPropositionChoisie(): ?Proposition
    {
        return $this->propositionChoisie;
    }

    public function setPropositionChoisie(?Proposition $propositionChoisie): self
    {
        $this->propositionChoisie = $propositionChoisie;

        return $this;
    }



    public function getReservation(): ?Reservation
    {
        return $this->reservation;
    }

    public function setReservation(?Reservation $reservation): self
    {
        // unset the owning side of the relation if necessary
        if ($reservation === null && $this->reservation !== null) {
            $this->reservation->setIntervention(null);
        }

        // set the owning side of the relation if necessary
        if ($reservation !== null && $reservation->getIntervention() !== $this) {
            $reservation->setIntervention($this);
        }

        $this->reservation = $reservation;

        return $this;
    }



    public function getMandatCerfa(): ?MandatCerfa
    {
        return $this->mandatCerfa;
    }

    public function setMandatCerfa(?MandatCerfa $mandatCerfa): self
    {
        $this->mandatCerfa = $mandatCerfa;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getDateInter():?int
    {
        return $this->dateInter;
    }


    public function setDateInter($dateInter): self
    {
        $this->dateInter = $dateInter;
        return $this;
    }

    public function getIntemperie(): ?array
    {
        return $this->intemperie;
    }

    public function setIntemperie(?array $intemperie): self
    {
        $this->intemperie = $intemperie;

        return $this;
    }

    public function getRenoncementDelaiRetract(): ?bool
    {
        return $this->RenoncementDelaiRetract;
    }

    public function setRenoncementDelaiRetract(bool $RenoncementDelaiRetract): self
    {
        $this->RenoncementDelaiRetract = $RenoncementDelaiRetract;

        return $this;
    }

    public function getMAP(): ?MAP
    {
        return $this->mAP;
    }

    public function setMAP(?MAP $mAP): self
    {
        $this->mAP = $mAP;

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
            $rapport->setIntervention($this);
        }

        return $this;
    }

    public function removeRapport(Rapport $rapport): self
    {
        if ($this->rapports->removeElement($rapport)) {
            // set the owning side to null (unless already changed)
            if ($rapport->getIntervention() === $this) {
                $rapport->setIntervention(null);
            }
        }

        return $this;
    }

    public function getFactures(): ?Factures
    {
        return $this->factures;
    }

    public function setFactures(?Factures $factures): self
    {
        // unset the owning side of the relation if necessary
        if ($factures === null && $this->factures !== null) {
            $this->factures->setIntervention(null);
        }

        // set the owning side of the relation if necessary
        if ($factures !== null && $factures->getIntervention() !== $this) {
            $factures->setIntervention($this);
        }

        $this->factures = $factures;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getMeteoInter(): ?MeteoInter
    {
        return $this->meteoInter;
    }

    public function setMeteoInter(?MeteoInter $meteoInter): self
    {
        // unset the owning side of the relation if necessary
        if ($meteoInter === null && $this->meteoInter !== null) {
            $this->meteoInter->setIntervention(null);
        }

        // set the owning side of the relation if necessary
        if ($meteoInter !== null && $meteoInter->getIntervention() !== $this) {
            $meteoInter->setIntervention($this);
        }

        $this->meteoInter = $meteoInter;

        return $this;
    }

    /**
     * @return Collection|Travaux[]
     */
    public function getTravauxes(): Collection
    {
        return $this->travauxes;
    }

    public function addTravaux(Travaux $travaux): self
    {
        if (!$this->travauxes->contains($travaux)) {
            $this->travauxes[] = $travaux;
            $travaux->addIntervention($this);
        }

        return $this;
    }

    public function removeTravaux(Travaux $travaux): self
    {
        if ($this->travauxes->removeElement($travaux)) {
            $travaux->removeIntervention($this);
        }

        return $this;
    }

    public function getBudgetInter(): ?BudgetInter
    {
        return $this->budgetInter;
    }

    public function setBudgetInter(?BudgetInter $budgetInter): self
    {
        // unset the owning side of the relation if necessary
        if ($budgetInter === null && $this->budgetInter !== null) {
            $this->budgetInter->setIntervention(null);
        }

        // set the owning side of the relation if necessary
        if ($budgetInter !== null && $budgetInter->getIntervention() !== $this) {
            $budgetInter->setIntervention($this);
        }

        $this->budgetInter = $budgetInter;

        return $this;
    }

    /**
     * @return Collection|ContrainteInter[]
     */
    public function getContrainteInters(): Collection
    {
        return $this->contrainteInters;
    }

    public function addContrainteInter(ContrainteInter $contrainteInter): self
    {
        if (!$this->contrainteInters->contains($contrainteInter)) {
            $this->contrainteInters[] = $contrainteInter;
            $contrainteInter->setIntervention($this);
        }

        return $this;
    }

    public function removeContrainteInter(ContrainteInter $contrainteInter): self
    {
        if ($this->contrainteInters->removeElement($contrainteInter)) {
            // set the owning side to null (unless already changed)
            if ($contrainteInter->getIntervention() === $this) {
                $contrainteInter->setIntervention(null);
            }
        }

        return $this;
    }

    public function getNbrePhoto(): ?int
    {
        return $this->nbrePhoto;
    }

    public function setNbrePhoto(?int $nbrePhoto): self
    {
        $this->nbrePhoto = $nbrePhoto;

        return $this;
    }

    public function getNbreVideo(): ?int
    {
        return $this->nbreVideo;
    }

    public function setNbreVideo(?int $nbreVideo): self
    {
        $this->nbreVideo = $nbreVideo;

        return $this;
    }

    public function getDateWitch(): ?\DateTimeInterface
    {
        return $this->dateWitch;
    }

    public function setDateWitch(?\DateTimeInterface $dateWitch): self
    {
        $this->dateWitch = $dateWitch;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(?\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(?\DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin;

        return $this;
    }


}
