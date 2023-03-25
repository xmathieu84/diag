<?php

namespace App\Entity;

use App\Repository\DocSousDossierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DocSousDossierRepository::class)
 */
class DocSousDossier
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=SousDossier::class, inversedBy="docSousDossiers")
     */
    private $sousDossier;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $libelle;

    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     */
    private $fichier;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateDevalidite;

    /**
     * @ORM\Column(type="dateinterval", nullable=true)
     */
    private $delaiAlerte;

    /**
     * @ORM\OneToMany(targetEntity=Contact::class, mappedBy="docSousDossier")
     */
    private $contact;

    /**
     * @ORM\OneToMany(targetEntity=Annotation::class, mappedBy="docSousDossier")
     */
    private $annotations;

    /**
     * @ORM\Column(type="datetime",nullable=true)
     */
    private $date;

    public function __construct()
    {
        $this->contact = new ArrayCollection();
        $this->annotations = new ArrayCollection();
        $this->date = new \DateTime('NOW',new \DateTimeZone('Europe/Paris'));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSousDossier(): ?SousDossier
    {
        return $this->sousDossier;
    }

    public function setSousDossier(?SousDossier $sousDossier): self
    {
        $this->sousDossier = $sousDossier;

        return $this;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getFichier(): ?string
    {
        return $this->fichier;
    }

    public function setFichier(string $fichier): self
    {
        $this->fichier = $fichier;

        return $this;
    }

    public function getDateDevalidite(): ?\DateTimeInterface
    {
        return $this->dateDevalidite;
    }

    public function setDateDevalidite(?\DateTimeInterface $dateDevalidite): self
    {
        $this->dateDevalidite = $dateDevalidite;

        return $this;
    }

    public function getDelaiAlerte(): ?\DateInterval
    {
        return $this->delaiAlerte;
    }

    public function setDelaiAlerte(?\DateInterval $delaiAlerte): self
    {
        $this->delaiAlerte = $delaiAlerte;

        return $this;
    }

    /**
     * @return Collection|Contact[]
     */
    public function getContact(): Collection
    {
        return $this->contact;
    }

    public function addContact(Contact $contact): self
    {
        if (!$this->contact->contains($contact)) {
            $this->contact[] = $contact;
            $contact->setDocSousDossier($this);
        }

        return $this;
    }

    public function removeContact(Contact $contact): self
    {
        if ($this->contact->removeElement($contact)) {
            // set the owning side to null (unless already changed)
            if ($contact->getDocSousDossier() === $this) {
                $contact->setDocSousDossier(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|annotation[]
     */
    public function getAnnotations(): Collection
    {
        return $this->annotations;
    }

    public function addAnnotation(annotation $annotation): self
    {
        if (!$this->annotations->contains($annotation)) {
            $this->annotations[] = $annotation;
            $annotation->setDocSousDossier($this);
        }

        return $this;
    }

    public function removeAnnotation(annotation $annotation): self
    {
        if ($this->annotations->removeElement($annotation)) {
            // set the owning side to null (unless already changed)
            if ($annotation->getDocSousDossier() === $this) {
                $annotation->setDocSousDossier(null);
            }
        }

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
}
