<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity(repositoryClass="App\Repository\CoordonneesRepository")
 *
 */
class Coordonnees
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     *
     *
     */
    private $latitude;

    /**
     * @ORM\Column(type="float")
     * @Assert\Regex(pattern="/[0-9.,]/",message="Caractères non autorisés")
     *
     *
     */
    private $longitude;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Assert\Regex(pattern="/[0-9.,]/",message="Caractères non autorisés")
     *
     */
    private $latMinInter;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Assert\Regex(pattern="/[0-9.,]/",message="Caractères non autorisés")
     */
    private $latMaxInter;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Assert\Regex(pattern="/[0-9.,]/",message="Caractères non autorisés")
     */
    private $lonMinInter;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Assert\Regex(pattern="/[0-9.,]/",message="Caractères non autorisés")
     */
    private $lonMaxInter;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

   

    public function getLatMinInter(): ?float
    {
        return $this->latMinInter;
    }

    public function setLatMinInter(?float $latMinInter): self
    {
        $this->latMinInter = $latMinInter;

        return $this;
    }

    public function getLatMaxInter(): ?float
    {
        return $this->latMaxInter;
    }

    public function setLatMaxInter(?float $latMaxInter): self
    {
        $this->latMaxInter = $latMaxInter;

        return $this;
    }

    public function getLonMinInter(): ?float
    {
        return $this->lonMinInter;
    }

    public function setLonMinInter(?float $lonMinInter): self
    {
        $this->lonMinInter = $lonMinInter;

        return $this;
    }

    public function getLonMaxInter(): ?float
    {
        return $this->lonMaxInter;
    }

    public function setLonMaxInter(?float $lonMaxInter): self
    {
        $this->lonMaxInter = $lonMaxInter;

        return $this;
    }
}
