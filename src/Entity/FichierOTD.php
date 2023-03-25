<?php

namespace App\Entity;

use App\Repository\FichierOTDRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FichierOTDRepository::class)
 */
class FichierOTD
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
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mail;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ville;

    /**
     * @ORM\OneToOne(targetEntity=CoorOtd::class, mappedBy="otd", cascade={"persist", "remove"})
     */
    private $coorOtd;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $telephone;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $desabonner;



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

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(?string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function getCoorOtd(): ?CoorOtd
    {
        return $this->coorOtd;
    }

    public function setCoorOtd(?CoorOtd $coorOtd): self
    {
        // unset the owning side of the relation if necessary
        if ($coorOtd === null && $this->coorOtd !== null) {
            $this->coorOtd->setOtd(null);
        }

        // set the owning side of the relation if necessary
        if ($coorOtd !== null && $coorOtd->getOtd() !== $this) {
            $coorOtd->setOtd($this);
        }

        $this->coorOtd = $coorOtd;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getDesabonner(): ?bool
    {
        return $this->desabonner;
    }

    public function setDesabonner(?bool $desabonner): self
    {
        $this->desabonner = $desabonner;

        return $this;
    }


}
