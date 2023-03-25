<?php

namespace App\Entity;

use JetBrains\PhpStorm\Pure;
use JsonSerializable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RapportRepository")
 */
class Rapport implements JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Regex(pattern="/[A-Za-z0-9_'.,]/",message="Le message est invalide")
     */
    private $rap_fichier;

    /**
     * @ORM\Column(type="json",nullable=true)
     * 
     */
    private $rap_resume = [];

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Regex(pattern="/[A-Za-z0-9_'.,]/",message="Le message est invalide")
     */
    private $rap_messhid;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\File(mimeTypes={ "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet","text/csv" })
     */
    private $donnees_telemetrique;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\File(mimeTypes={ "application/pdf" })
     */
    private $cerfa_inter;

    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     */
    private $statu_rapport;



    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Photo", mappedBy="rapport",cascade={"persist"})
     */
    private $photos;



    /**
     * @ORM\Column(type="string", length=6, nullable=true)
     */
    private $codeRecherche;



    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $archive;


    /**
     * @ORM\OneToMany(targetEntity=Video::class, mappedBy="rapport")
     */
    private $videos;

    /**
     * @ORM\ManyToMany(targetEntity=Demandeur::class, inversedBy="rapports")
     */
    private $consultant;

    /**
     * @ORM\OneToMany(targetEntity=ConsultantHDD::class, mappedBy="rapport")
     */
    private $consultantHDDs;

    /**
     * @ORM\ManyToOne(targetEntity=Intervention::class, inversedBy="rapports")
     */
    private $intervention;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $rapportPdf;





     public function __construct()
    {
        $this->photos = new ArrayCollection();
        $this->videos = new ArrayCollection();
        $this->consultant = new ArrayCollection();
        $this->consultantHDDs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }





    public function getRapFichier(): ?string
    {
        return $this->rap_fichier;
    }

    public function setRapFichier(?string $rap_fichier): self
    {
        $this->rap_fichier = $rap_fichier;

        return $this;
    }

    public function getRapResume(): ?array
    {
        return $this->rap_resume;
    }

    public function setRapResume(array $rap_resume): self
    {
        $this->rap_resume = $rap_resume;

        return $this;
    }

    public function getRapMesshid(): ?string
    {
        return $this->rap_messhid;
    }

    public function setRapMesshid(?string $rap_messhid): self
    {
        $this->rap_messhid = $rap_messhid;

        return $this;
    }

    public function getDonneesTelemetrique(): ?string
    {
        return $this->donnees_telemetrique;
    }

    public function setDonneesTelemetrique(?string $donnees_telemetrique): self
    {
        $this->donnees_telemetrique = $donnees_telemetrique;

        return $this;
    }

    public function getCerfaInter(): ?string
    {
        return $this->cerfa_inter;
    }

    public function setCerfaInter(?string $cerfa_inter): self
    {
        $this->cerfa_inter = $cerfa_inter;

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

    public function getStatuRapport(): ?string
    {
        return $this->statu_rapport;
    }

    public function setStatuRapport(string $statu_rapport): self
    {
        $this->statu_rapport = $statu_rapport;

        return $this;
    }



    /**
     * @return Collection|Photo[]
     */
    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function addPhoto(Photo $photo)
    {
        if (!$this->photos->contains($photo)) {
            $this->photos[] = $photo;
            $photo->setRapport($this);
        }

        return $this;
    }

    public function removePhoto(Photo $photo): self
    {
        if ($this->photos->contains($photo)) {
            $this->photos->removeElement($photo);
            // set the owning side to null (unless already changed)
            if ($photo->getRapport() === $this) {
                $photo->setRapport(null);
            }
        }

        return $this;
    }



    public function getCodeRecherche(): ?string
    {
        return $this->codeRecherche;
    }

    public function setCodeRecherche(?string $codeRecherche): self
    {
        $this->codeRecherche = $codeRecherche;

        return $this;
    }



    public function getArchive(): ?string
    {
        return $this->archive;
    }

    public function setArchive(?string $archive): self
    {
        $this->archive = $archive;

        return $this;
    }

    



    /**
     * @return Collection|Video[]
     */
    public function getVideos(): Collection
    {
        return $this->videos;
    }

    public function addVideo(Video $video): self
    {
        if (!$this->videos->contains($video)) {
            $this->videos[] = $video;
            $video->setRapport($this);
        }

        return $this;
    }

    public function removeVideo(Video $video): self
    {
        if ($this->videos->removeElement($video)) {
            // set the owning side to null (unless already changed)
            if ($video->getRapport() === $this) {
                $video->setRapport(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Demandeur[]
     */
    public function getConsultant(): Collection
    {
        return $this->consultant;
    }

    public function addConsultant(Demandeur $consultant): self
    {
        if (!$this->consultant->contains($consultant)) {
            $this->consultant[] = $consultant;
        }

        return $this;
    }

    public function removeConsultant(Demandeur $consultant): self
    {
        $this->consultant->removeElement($consultant);

        return $this;
    }

    /**
     * @return Collection|ConsultantHDD[]
     */
    public function getConsultantHDDs(): Collection
    {
        return $this->consultantHDDs;
    }

    public function addConsultantHDD(ConsultantHDD $consultantHDD): self
    {
        if (!$this->consultantHDDs->contains($consultantHDD)) {
            $this->consultantHDDs[] = $consultantHDD;
            $consultantHDD->setRapport($this);
        }

        return $this;
    }

    public function removeConsultantHDD(ConsultantHDD $consultantHDD): self
    {
        if ($this->consultantHDDs->removeElement($consultantHDD)) {
            // set the owning side to null (unless already changed)
            if ($consultantHDD->getRapport() === $this) {
                $consultantHDD->setRapport(null);
            }
        }

        return $this;
    }

    public function getIntervention(): ?Intervention
    {
        return $this->intervention;
    }

    public function setIntervention(?Intervention $intervention): self
    {
        $this->intervention = $intervention;

        return $this;
    }

    public function getRapportPdf(): ?string
    {
        return $this->rapportPdf;
    }

    public function setRapportPdf(?string $rapportPdf): self
    {
        $this->rapportPdf = $rapportPdf;

        return $this;
    }


}
