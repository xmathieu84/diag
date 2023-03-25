<?php

namespace App\Entity;

use App\Repository\SujetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity(repositoryClass=SujetRepository::class)
 */
class Sujet
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(pattern="/[A-Za-z0-9_'.,]/",message="Caractères non autorisé")
     */
    private $nom;

    /**
     * @ORM\Column(type="text")
     * @Assert\Regex(pattern="/[A-Za-z0-9_'.,]/",message="Caractères non autorisé")
     */
    private $contenu;

    /**
     * @ORM\ManyToOne(targetEntity=Categories::class, inversedBy="sujets")
     */
    private $categorie;

    /**
     * @ORM\OneToMany(targetEntity=ReonseForum::class, mappedBy="sujet")
     */
    private $reonseForums;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $redacteur;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateCreation;

    public function __construct()
    {
        $this->reonseForums = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): self
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getCategorie(): ?Categories
    {
        return $this->categorie;
    }

    public function setCategorie(?Categories $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }
    public function getRedacteur(): ?string
    {
        return $this->redacteur;
    }

    public function setRedacteur(string $redacteur): self
    {
        $this->redacteur = $redacteur;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * @return Collection|ReonseForum[]
     */
    public function getReonseForums(): Collection
    {
        return $this->reonseForums;
    }

    public function addReonseForum(ReonseForum $reonseForum): self
    {
        if (!$this->reonseForums->contains($reonseForum)) {
            $this->reonseForums[] = $reonseForum;
            $reonseForum->setSujet($this);
        }

        return $this;
    }

    public function removeReonseForum(ReonseForum $reonseForum): self
    {
        if ($this->reonseForums->contains($reonseForum)) {
            $this->reonseForums->removeElement($reonseForum);
            // set the owning side to null (unless already changed)
            if ($reonseForum->getSujet() === $this) {
                $reonseForum->setSujet(null);
            }
        }

        return $this;
    }
}
