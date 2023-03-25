<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AssurancesRepository")
 */
class Assurances
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;



    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(pattern="/[A-Za-z0-9_'.,]/",message="Le numéro est invalide est invalide")
     */
    private $ass_pro;

    /**
     * @ORM\Column(type="string", length=255)
     * 
     *@Assert\File(mimeTypes={ "application/pdf" }) 
     * 
     */
    private $ass_pro_fichier;



    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nomCompagnie;

    /**
     * @ORM\OneToOne(targetEntity=RcComplement::class, mappedBy="assurance", cascade={"persist"})
     */
    private $rcComplement;

    public function getId(): ?int
    {
        return $this->id;
    }



    public function getAssPro(): ?string
    {
        return $this->ass_pro;
    }

    public function setAssPro(string $ass_pro): self
    {
        $this->ass_pro = $ass_pro;

        return $this;
    }

    public function getAssProFichier(): ?string
    {
        return $this->ass_pro_fichier;
    }

    public function setAssProFichier(string $ass_pro_fichier): self
    {
        $this->ass_pro_fichier = $ass_pro_fichier;

        return $this;
    }



    public function getNomCompagnie(): ?string
    {
        return $this->nomCompagnie;
    }

    public function setNomCompagnie(string $nomCompagnie): self
    {
        $this->nomCompagnie = $nomCompagnie;

        return $this;
    }

    public function getRcComplement(): ?RcComplement
    {
        return $this->rcComplement;
    }

    public function setRcComplement(?RcComplement $rcComplement): self
    {
        // unset the owning side of the relation if necessary
        if ($rcComplement === null && $this->rcComplement !== null) {
            $this->rcComplement->setAssurance(null);
        }

        // set the owning side of the relation if necessary
        if ($rcComplement !== null && $rcComplement->getAssurance() !== $this) {
            $rcComplement->setAssurance($this);
        }

        $this->rcComplement = $rcComplement;

        return $this;
    }
}
