<?php

namespace App\Entity;

use App\Repository\AnnotationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AnnotationRepository::class)
 */
class Annotation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $titre;

    /**
     * @ORM\Column(type="text")
     */
    private $texte;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $auteur;

    /**
     * @ORM\ManyToOne(targetEntity=SousDossier::class, inversedBy="annotations")
     */
    private $sousDossier;

    /**
     * @ORM\ManyToOne(targetEntity=DocSousDossier::class, inversedBy="annotations")
     */
    private $docSousDossier;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getTexte(): ?string
    {
        return $this->texte;
    }

    public function setTexte(string $texte): self
    {
        $this->texte = $texte;

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

    public function getAuteur(): ?string
    {
        return $this->auteur;
    }

    public function setAuteur(string $auteur): self
    {
        $this->auteur = $auteur;

        return $this;
    }

    public function getSousDossier(): ?SousDOssier
    {
        return $this->sousDossier;
    }

    public function setSousDossier(?SousDOssier $sousDossier): self
    {
        $this->sousDossier = $sousDossier;

        return $this;
    }

    public function getDocSousDossier(): ?DocSousDossier
    {
        return $this->docSousDossier;
    }

    public function setDocSousDossier(?DocSousDossier $docSousDossier): self
    {
        $this->docSousDossier = $docSousDossier;

        return $this;
    }
}
