<?php

namespace App\Entity;



use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AdresseRepository")
 *
 *
 */
class Adresse implements JsonSerializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Regex(pattern="/[a-zA-Z0-9_']/",message="Le numéro de la voie est invalide")
     * @Groups ({"demandeur:read","demandeur:write","etape3"})
     *
     */
    private $numero;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(pattern="/[a-zA-Z0-9_']/",message="Le nom de la voie est invalide")
     * @Groups ({"demandeur:read","demandeur:write","etape3"})
     */
    private $nomVoie;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(pattern="/\d/",message="Le code postal est invalide")
     * @Groups ({"demandeur:read","demandeur:write","etape3"})
     *
     */
    private $codePostal;



    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(pattern="/[a-zA-Z0-9_']/",message="Le nom de la ville est invalide")
     * @Groups ({"demandeur:read","demandeur:write","etape3"})
     *
     */
    private $ville;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Coordonnees", cascade={ "persist","remove"})
     *
     *
     */
    private $coordonnees;

    /**
     * @ORM\ManyToOne(targetEntity=MailPrefecture::class, inversedBy="adresses")
     */
    private $departement;

    /**
     * @ORM\OneToOne(targetEntity=InterDiag::class, mappedBy="adresse", cascade={"persist", "remove"})
     */
    private $interDiag;






    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(?string $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getNomVoie(): ?string
    {
        return $this->nomVoie;
    }

    public function setNomVoie(string $nomVoie): self
    {
        $this->nomVoie = $nomVoie;

        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function setCodePostal(string $codePostal): self
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function adresseComplete(): string{
        return $this->numero.' '.$this->nomVoie.' '.$this->codePostal.' '.$this->ville;
    }


    public function getCoordonnees(): ?coordonnees
    {
        return $this->coordonnees;
    }

    public function setCoordonnees(?coordonnees $coordonnees): self
    {
        $this->coordonnees = $coordonnees;

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

    public function getDepartement(): ?MailPrefecture
    {
        return $this->departement;
    }

    public function setDepartement(?MailPrefecture $departement): self
    {
        $this->departement = $departement;

        return $this;
    }

    public function getInterDiag(): ?InterDiag
    {
        return $this->interDiag;
    }

    public function setInterDiag(?InterDiag $interDiag): self
    {
        // unset the owning side of the relation if necessary
        if ($interDiag === null && $this->interDiag !== null) {
            $this->interDiag->setAdresse(null);
        }

        // set the owning side of the relation if necessary
        if ($interDiag !== null && $interDiag->getAdresse() !== $this) {
            $interDiag->setAdresse($this);
        }

        $this->interDiag = $interDiag;

        return $this;
    }










}
