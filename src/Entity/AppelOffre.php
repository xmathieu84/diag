<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\AppelOffreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=AppelOffreRepository::class)
 *
 */
class AppelOffre
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ordre;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     */
    private $reference;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $denomination;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;



    /**
     * @ORM\Column(type="datetime")
     */
    private $DateRemiseProp;

    /**
     * @ORM\Column(type="datetime")
     */
    private $datePriseDecision;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $datePassationCommande;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateMaxLivraison;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $critereSelection;

    /**
     * @ORM\Column(type="text")
     */
    private $presentation;

    /**
     * @ORM\OneToOne(targetEntity=Restreint::class, mappedBy="appelOffre", cascade={"persist", "remove"})
     */
    private $restreint;

    /**
     * @ORM\OneToMany(targetEntity=Description::class, mappedBy="appelOffre")
     */
    private $descriptions;



    /**
     * @ORM\OneToMany(targetEntity=Contact::class, mappedBy="appelOffre")
     */
    private $contacts;

    /**
     * @ORM\OneToOne(targetEntity=Budget::class, cascade={"persist", "remove"})
     */
    private $budget;

    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     */
    private $categorie;

    /**
     * @ORM\ManyToOne(targetEntity=Agent::class, inversedBy="appelOffres")
     */
    private $agents;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\OneToMany(targetEntity=InfoComplementaire::class, mappedBy="appelOffres")
     */
    private $infoComplementaires;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $etat;

    /**
     * @ORM\OneToMany(targetEntity=DossierAo::class, mappedBy="AppelOffre")
     */
    private $dossierAos;

    /**
     * @ORM\OneToMany(targetEntity=ReponseAo::class, mappedBy="appel")
     */
    private $reponseAos;

    /**
     * @ORM\OneToOne(targetEntity=ReponseAo::class, inversedBy="appelOffre", cascade={"persist", "remove"})
     */
    private $reponseChoisie;



    public function __construct()
    {
        $this->descriptions = new ArrayCollection();
        $this->contacts = new ArrayCollection();
        $this->date = new \DateTime('NOW',new \DateTimeZone('Europe/Paris'));
        $this->infoComplementaires = new ArrayCollection();
        $this->dossierAos = new ArrayCollection();
        $this->reponseAos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrdre(): ?string
    {
        return $this->ordre;
    }

    public function setOrdre(?string $ordre): self
    {
        $this->ordre = $ordre;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getDenomination(): ?string
    {
        return $this->denomination;
    }

    public function setDenomination(?string $denomination): self
    {
        $this->denomination = $denomination;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }



    public function getDateRemiseProp(): ?\DateTime
    {
        return $this->DateRemiseProp;
    }

    public function setDateRemiseProp(\DateTime $DateRemiseProp): self
    {
        $this->DateRemiseProp = $DateRemiseProp;

        return $this;
    }

    public function getDatePriseDecision(): ?\DateTime
    {
        return $this->datePriseDecision;
    }

    public function setDatePriseDecision(\DateTime $datePriseDecision): self
    {
        $this->datePriseDecision = $datePriseDecision;

        return $this;
    }

    public function getDatePassationCommande(): ?\DateTime
    {
        return $this->datePassationCommande;
    }

    public function setDatePassationCommande(?\DateTime $datePassationCommande): self
    {
        $this->datePassationCommande = $datePassationCommande;

        return $this;
    }

    public function getDateMaxLivraison(): ?\DateTime
    {
        return $this->dateMaxLivraison;
    }

    public function setDateMaxLivraison(?\DateTime $dateMaxLivraison): self
    {
        $this->dateMaxLivraison = $dateMaxLivraison;

        return $this;
    }

    public function getCritereSelection(): ?string
    {
        return $this->critereSelection;
    }

    public function setCritereSelection(?string $critereSelection): self
    {
        $this->critereSelection = $critereSelection;

        return $this;
    }

    public function getPresentation(): ?string
    {
        return $this->presentation;
    }

    public function setPresentation(string $presentation): self
    {
        $this->presentation = $presentation;

        return $this;
    }

    public function getRestreint(): ?Restreint
    {
        return $this->restreint;
    }

    public function setRestreint(?Restreint $restreint): self
    {
        // unset the owning side of the relation if necessary
        if ($restreint === null && $this->restreint !== null) {
            $this->restreint->setAppelOffre(null);
        }

        // set the owning side of the relation if necessary
        if ($restreint !== null && $restreint->getAppelOffre() !== $this) {
            $restreint->setAppelOffre($this);
        }

        $this->restreint = $restreint;

        return $this;
    }

    /**
     * @return Collection|Description[]
     */
    public function getDescriptions(): Collection
    {
        return $this->descriptions;
    }

    public function addDescription(Description $description): self
    {
        if (!$this->descriptions->contains($description)) {
            $this->descriptions[] = $description;
            $description->setAppelOffre($this);
        }

        return $this;
    }

    public function removeDescription(Description $description): self
    {
        if ($this->descriptions->removeElement($description)) {
            // set the owning side to null (unless already changed)
            if ($description->getAppelOffre() === $this) {
                $description->setAppelOffre(null);
            }
        }

        return $this;
    }



    /**
     * @return Collection|Contact[]
     */
    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    public function addContact(Contact $contact): self
    {
        if (!$this->contacts->contains($contact)) {
            $this->contacts[] = $contact;
            $contact->setAppelOffre($this);
        }

        return $this;
    }

    public function removeContact(Contact $contact): self
    {
        if ($this->contacts->removeElement($contact)) {
            // set the owning side to null (unless already changed)
            if ($contact->getAppelOffre() === $this) {
                $contact->setAppelOffre(null);
            }
        }

        return $this;
    }

    public function getBudget(): ?Budget
    {
        return $this->budget;
    }

    public function setBudget(?Budget $budget): self
    {
        $this->budget = $budget;

        return $this;
    }

    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    public function setCategorie(string $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getAgents(): ?Agent
    {
        return $this->agents;
    }

    public function setAgents(?Agent $agents): self
    {
        $this->agents = $agents;

        return $this;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return Collection|InfoComplementaire[]
     */
    public function getInfoComplementaires(): Collection
    {
        return $this->infoComplementaires;
    }

    public function addInfoComplementaire(InfoComplementaire $infoComplementaire): self
    {
        if (!$this->infoComplementaires->contains($infoComplementaire)) {
            $this->infoComplementaires[] = $infoComplementaire;
            $infoComplementaire->setAppelOffres($this);
        }

        return $this;
    }

    public function removeInfoComplementaire(InfoComplementaire $infoComplementaire): self
    {
        if ($this->infoComplementaires->removeElement($infoComplementaire)) {
            // set the owning side to null (unless already changed)
            if ($infoComplementaire->getAppelOffres() === $this) {
                $infoComplementaire->setAppelOffres(null);
            }
        }

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(?string $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * @return Collection|DossierAo[]
     */
    public function getDossierAos(): Collection
    {
        return $this->dossierAos;
    }

    public function addDossierAo(DossierAo $dossierAo): self
    {
        if (!$this->dossierAos->contains($dossierAo)) {
            $this->dossierAos[] = $dossierAo;
            $dossierAo->setAppelOffre($this);
        }

        return $this;
    }

    public function removeDossierAo(DossierAo $dossierAo): self
    {
        if ($this->dossierAos->removeElement($dossierAo)) {
            // set the owning side to null (unless already changed)
            if ($dossierAo->getAppelOffre() === $this) {
                $dossierAo->setAppelOffre(null);
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
            $reponseAo->setAppel($this);
        }

        return $this;
    }

    public function removeReponseAo(ReponseAo $reponseAo): self
    {
        if ($this->reponseAos->removeElement($reponseAo)) {
            // set the owning side to null (unless already changed)
            if ($reponseAo->getAppel() === $this) {
                $reponseAo->setAppel(null);
            }
        }

        return $this;
    }

    public function getReponseChoisie(): ?ReponseAo
    {
        return $this->reponseChoisie;
    }

    public function setReponseChoisie(?ReponseAo $reponseChoisie): self
    {
        $this->reponseChoisie = $reponseChoisie;

        return $this;
    }


}
