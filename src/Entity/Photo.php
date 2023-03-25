<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PhotoRepository")
 */
class Photo
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\File(mimeTypes={ "image/png", "image/jpeg","image/jpg" })
     *
     */
    private $nom;

    /**
     * @Groups ("etape4")
     *
     */
    private $photoBase64;



    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Rapport", inversedBy="photos")
     */
    private $rapport;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Intervention", inversedBy="photoInter",cascade = {"persist"})
     */
    private $intervention;



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

    public function getRapport(): ?Rapport
    {
        return $this->rapport;
    }

    public function setRapport(?Rapport $rapport): self
    {
        $this->rapport = $rapport;

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



    public function getPhotoBase64():?string
    {
        return $this->photoBase64;
    }


    public function setPhotoBase64($photoBase64)
    {
        $this->photoBase64 = $photoBase64;
        return $this;
    }
}
