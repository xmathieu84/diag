<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\AccueilController;
use App\Repository\SalarieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SalarieRepository")
 */

class Salarie
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;



    /**
     * @ORM\Column(type="integer",nullable = true)
     * @Assert\Regex(pattern="/[0-9]/",message="Le nombre saisi est invalide")
     */
    private $periInter;


    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Adresse", cascade={"persist", "remove"})
     */
    private $adresse;
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Indisponibilite", mappedBy="salarie")
     */
    private $indisponibilites;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Annulation", mappedBy="salarie")
     */
    private $annulations;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Note", mappedBy="salarie")
     */
    private $notes;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $validation;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Reservation", mappedBy="salarie")
     */
    private $reservations;

    /**
     * @ORM\OneToOne(targetEntity=Civilite::class, inversedBy="salarie", cascade={"persist", "remove"})
     */
    private $civilite;

    /**
     * @ORM\OneToMany(targetEntity=TauxHoraire::class, mappedBy="salarie")
     */
    private $tauxHoraires;

    /**
     * @ORM\OneToMany(targetEntity=Proposition::class, mappedBy="salarie")
     */
    private $propositions;

    /**
     * @ORM\OneToOne(targetEntity=Telephone::class, cascade={"persist", "remove"})
     */
    private $telephone;

    /**
     * @ORM\OneToOne(targetEntity=AlphaTango::class, cascade={"persist", "remove"})
     */
    private $alphaTango;

    /**
     * @ORM\OneToOne(targetEntity=LicenceDgac::class, cascade={"persist", "remove"})
     */
    private $licenceDgac;

    /**
     * @ORM\ManyToOne(targetEntity=Entreprise::class, inversedBy="salaries",fetch="EAGER")
     */
    private $entreprise;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="salarie", cascade={"persist", "remove"})
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=MissionOdi::class, mappedBy="odi")
     */
    private $missionOdi;

    /**
     * @ORM\OneToMany(targetEntity=PackOdi::class, mappedBy="odi")
     */
    private $packOdis;

    /**
     * @ORM\OneToMany(targetEntity=FichierPrix::class, mappedBy="odi")
     */
    private $fichierPrixes;

    /**
     * @ORM\OneToMany(targetEntity=EtatPrixOdi::class, mappedBy="odi")
     */
    private $etatPrixOdis;

    /**
     * @ORM\OneToOne(targetEntity=RemiseTemps::class, mappedBy="odi", cascade={"persist", "remove"})
     */
    private $remiseTemps;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $marketPlace;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isHonneur;

    /**
     * @ORM\OneToMany(targetEntity=PrixPrelevement::class, mappedBy="odi")
     */
    private $prixPrelevements;

    /**
     * @ORM\OneToMany(targetEntity=InterDiag::class, mappedBy="odi")
     */
    private $interDiags;







    public function __construct()
    {

        $this->indisponibilites = new ArrayCollection();
        $this->annulations = new ArrayCollection();
        $this->notes = new ArrayCollection();
        $this->reservations = new ArrayCollection();
        $this->tauxHoraires = new ArrayCollection();
        $this->propositions = new ArrayCollection();
        $this->reponseAo = new ArrayCollection();
        $this->missionOdi = new ArrayCollection();
        $this->packOdis = new ArrayCollection();
        $this->fichierPrixes = new ArrayCollection();
        $this->etatPrixOdis = new ArrayCollection();
        $this->prixPrelevements = new ArrayCollection();
        $this->interDiags = new ArrayCollection();




    }




    public function getId(): ?int
    {
        return $this->id;
    }







    public function getPeriInter(): ?int
    {
        return $this->periInter;
    }

    public function setPeriInter(int $periInter): self
    {
        $this->periInter = $periInter;

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




    /**
     * @return Collection|Indisponibilite[]
     */
    public function getIndisponibilites(): Collection
    {
        return $this->indisponibilites;
    }

    public function addIndisponibilite(Indisponibilite $indisponibilite): self
    {
        if (!$this->indisponibilites->contains($indisponibilite)) {
            $this->indisponibilites[] = $indisponibilite;
            $indisponibilite->setSalarie($this);
        }

        return $this;
    }

    public function removeIndisponibilite(Indisponibilite $indisponibilite): self
    {
        if ($this->indisponibilites->contains($indisponibilite)) {
            $this->indisponibilites->removeElement($indisponibilite);
            // set the owning side to null (unless already changed)
            if ($indisponibilite->getSalarie() === $this) {
                $indisponibilite->setSalarie(null);
            }
        }

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
            $annulation->setSalarie($this);
        }

        return $this;
    }

    public function removeAnnulation(Annulation $annulation): self
    {
        if ($this->annulations->contains($annulation)) {
            $this->annulations->removeElement($annulation);
            // set the owning side to null (unless already changed)
            if ($annulation->getSalarie() === $this) {
                $annulation->setSalarie(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Note[]
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Note $note): self
    {
        if (!$this->notes->contains($note)) {
            $this->notes[] = $note;
            $note->setSalarie($this);
        }

        return $this;
    }

    public function removeNote(Note $note): self
    {
        if ($this->notes->contains($note)) {
            $this->notes->removeElement($note);
            // set the owning side to null (unless already changed)
            if ($note->getSalarie() === $this) {
                $note->setSalarie(null);
            }
        }

        return $this;
    }

    public function getValidation(): ?string
    {
        return $this->validation;
    }

    public function setValidation(string $validation): self
    {
        $this->validation = $validation;

        return $this;
    }

    /**
     * @return Collection|Reservation[]
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations[] = $reservation;
            $reservation->setSalarie($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->contains($reservation)) {
            $this->reservations->removeElement($reservation);
            // set the owning side to null (unless already changed)
            if ($reservation->getSalarie() === $this) {
                $reservation->setSalarie(null);
            }
        }

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
     * @return Collection|TauxHoraire[]
     */
    public function getTauxHoraires(): Collection
    {
        return $this->tauxHoraires;
    }

    public function addTauxHoraire(TauxHoraire $tauxHoraire): self
    {
        if (!$this->tauxHoraires->contains($tauxHoraire)) {
            $this->tauxHoraires[] = $tauxHoraire;
            $tauxHoraire->setSalarie($this);
        }

        return $this;
    }

    public function removeTauxHoraire(TauxHoraire $tauxHoraire): self
    {
        if ($this->tauxHoraires->contains($tauxHoraire)) {
            $this->tauxHoraires->removeElement($tauxHoraire);
            // set the owning side to null (unless already changed)
            if ($tauxHoraire->getSalarie() === $this) {
                $tauxHoraire->setSalarie(null);
            }
        }

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
            $proposition->setSalarie($this);
        }

        return $this;
    }

    public function removeProposition(Proposition $proposition): self
    {
        if ($this->propositions->contains($proposition)) {
            $this->propositions->removeElement($proposition);
            // set the owning side to null (unless already changed)
            if ($proposition->getSalarie() === $this) {
                $proposition->setSalarie(null);
            }
        }

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

    public function getAlphaTango(): ?AlphaTango
    {
        return $this->alphaTango;
    }

    public function setAlphaTango(?AlphaTango $alphaTango): self
    {
        $this->alphaTango = $alphaTango;

        return $this;
    }

    public function getLicenceDgac(): ?LicenceDgac
    {
        return $this->licenceDgac;
    }

    public function setLicenceDgac(?LicenceDgac $licenceDgac): self
    {
        $this->licenceDgac = $licenceDgac;

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

    /**
     * @return Collection|ReponseAo[]
     */
    public function getReponseAo(): Collection
    {
        return $this->reponseAo;
    }

    public function addReponseAo(ReponseAo $reponseAo): self
    {
        if (!$this->reponseAo->contains($reponseAo)) {
            $this->reponseAo[] = $reponseAo;
            $reponseAo->setSalarie($this);
        }

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
     * @return Collection<int, MissionOdi>
     */
    public function getDetailPrixes(): Collection
    {

        return $this->missionOdi;
    }

    public function addDetailPrix(MissionOdi $detailPrix): self
    {
        if (!$this->missionOdi->contains($detailPrix)) {
            $this->missionOdi[] = $detailPrix;
            $detailPrix->setOdi($this);
        }

        return $this;
    }
    public function getTimeNull():bool{
        foreach ($this->missionOdi as $mission){
            foreach ($mission->getPrixOdiMissions() as $prix){
                if ($prix->getTemps()===null){
                    return false;
                    break;
                }
            }
        }
        return true;

    }
    public function removeDetailPrix(MissionOdi $detailPrix): self
    {
        if ($this->missionOdi->removeElement($detailPrix)) {
            // set the owning side to null (unless already changed)
            if ($detailPrix->getOdi() === $this) {
                $detailPrix->setOdi(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PackOdi>
     */
    public function getPackOdis(): Collection
    {
        return $this->packOdis;
    }

    public function addPackOdi(PackOdi $packOdi): self
    {
        if (!$this->packOdis->contains($packOdi)) {
            $this->packOdis[] = $packOdi;
            $packOdi->setOdi($this);
        }

        return $this;
    }

    public function removePackOdi(PackOdi $packOdi): self
    {
        if ($this->packOdis->removeElement($packOdi)) {
            // set the owning side to null (unless already changed)
            if ($packOdi->getOdi() === $this) {
                $packOdi->setOdi(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FichierPrix>
     */
    public function getFichierPrixes(): Collection
    {
        return $this->fichierPrixes;
    }

    public function addFichierPrix(FichierPrix $fichierPrix): self
    {
        if (!$this->fichierPrixes->contains($fichierPrix)) {
            $this->fichierPrixes[] = $fichierPrix;
            $fichierPrix->setOdi($this);
        }

        return $this;
    }

    public function removeFichierPrix(FichierPrix $fichierPrix): self
    {
        if ($this->fichierPrixes->removeElement($fichierPrix)) {
            // set the owning side to null (unless already changed)
            if ($fichierPrix->getOdi() === $this) {
                $fichierPrix->setOdi(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, EtatPrixOdi>
     */
    public function getEtatPrixOdis(): Collection
    {
        return $this->etatPrixOdis;
    }

    public function addEtatPrixOdi(EtatPrixOdi $etatPrixOdi): self
    {
        if (!$this->etatPrixOdis->contains($etatPrixOdi)) {
            $this->etatPrixOdis[] = $etatPrixOdi;
            $etatPrixOdi->setOdi($this);
        }

        return $this;
    }

    public function removeEtatPrixOdi(EtatPrixOdi $etatPrixOdi): self
    {
        if ($this->etatPrixOdis->removeElement($etatPrixOdi)) {
            // set the owning side to null (unless already changed)
            if ($etatPrixOdi->getOdi() === $this) {
                $etatPrixOdi->setOdi(null);
            }
        }

        return $this;
    }

    public function getRemiseTemps(): ?RemiseTemps
    {
        return $this->remiseTemps;
    }

    public function setRemiseTemps(?RemiseTemps $remiseTemps): self
    {
        // unset the owning side of the relation if necessary
        if ($remiseTemps === null && $this->remiseTemps !== null) {
            $this->remiseTemps->setOdi(null);
        }

        // set the owning side of the relation if necessary
        if ($remiseTemps !== null && $remiseTemps->getOdi() !== $this) {
            $remiseTemps->setOdi($this);
        }

        $this->remiseTemps = $remiseTemps;

        return $this;
    }

    public function getMarketPlace(): ?bool
    {
        return $this->marketPlace;
    }

    public function setMarketPlace(?bool $marketPlace): self
    {
        $this->marketPlace = $marketPlace;

        return $this;
    }

    public function getIsHonneur(): ?bool
    {
        return $this->isHonneur;
    }

    public function setIsHonneur(?bool $isHonneur): self
    {
        $this->isHonneur = $isHonneur;

        return $this;
    }

    /**
     * @return Collection<int, PrixPrelevement>
     */
    public function getPrixPrelevements(): Collection
    {
        return $this->prixPrelevements;
    }

    public function addPrixPrelevement(PrixPrelevement $prixPrelevement): self
    {
        if (!$this->prixPrelevements->contains($prixPrelevement)) {
            $this->prixPrelevements[] = $prixPrelevement;
            $prixPrelevement->setOdi($this);
        }

        return $this;
    }

    public function removePrixPrelevement(PrixPrelevement $prixPrelevement): self
    {
        if ($this->prixPrelevements->removeElement($prixPrelevement)) {
            // set the owning side to null (unless already changed)
            if ($prixPrelevement->getOdi() === $this) {
                $prixPrelevement->setOdi(null);
            }
        }

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
            $interDiag->setOdi($this);
        }

        return $this;
    }

    public function removeInterDiag(InterDiag $interDiag): self
    {
        if ($this->interDiags->removeElement($interDiag)) {
            // set the owning side to null (unless already changed)
            if ($interDiag->getOdi() === $this) {
                $interDiag->setOdi(null);
            }
        }

        return $this;
    }




}
